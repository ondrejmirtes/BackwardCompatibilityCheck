<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\LocateDependencies;

use Composer\Filter\PlatformRequirementFilter\IgnoreAllPlatformRequirementFilter;
use Composer\Installer;
use Psl;
use Psl\Env;
use Psl\Filesystem;
use Roave\BackwardCompatibility\SourceLocator\ReplaceSourcePathOfLocatedSources;
use PHPStan\BetterReflection\SourceLocator\Ast\Locator;
use PHPStan\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use PHPStan\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\Composer\Factory\MakeLocatorForInstalledJson;
use PHPStan\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

final class LocateDependenciesViaComposer implements LocateDependencies
{
    /** @psalm-var callable(string): Installer $makeComposerInstaller */
    private $makeComposerInstaller;

    /** @psalm-param callable(string): Installer $makeComposerInstaller */
    public function __construct(
        callable $makeComposerInstaller,
        private Locator $astLocator,
    ) {
        // This is needed because the CWD of composer cannot be changed at runtime, but only at startup
        $this->makeComposerInstaller = $makeComposerInstaller;
    }

    public function __invoke(string $installationPath, bool $includeDevelopmentDependencies): SourceLocator
    {
        Psl\invariant(Filesystem\is_file($installationPath . '/composer.json'), 'Could not locate composer.json within installation path.');

        $this->runInDirectory(function () use ($installationPath, $includeDevelopmentDependencies): void {
            $installer = ($this->makeComposerInstaller)($installationPath);

            // Some defaults needed for this specific implementation:
            $installer->setDevMode($includeDevelopmentDependencies);
            $installer->setDumpAutoloader(false);
            /**
             * @psalm-suppress DeprecatedMethod we will keep using the deprecated API until the next major release
             *                 of composer, as we otherwise need to re-design how an {@see Installer} is constructed.
             */
            $installer->setRunScripts(false);
            $installer->setPlatformRequirementFilter(new IgnoreAllPlatformRequirementFilter());

            $installer->run();
        }, $installationPath);

        $astLocator = new ReplaceSourcePathOfLocatedSources($this->astLocator, $installationPath);

        return new AggregateSourceLocator([
            new PhpInternalSourceLocator($astLocator, new ReflectionSourceStubber()),
            (new MakeLocatorForInstalledJson())($installationPath, $astLocator),
        ]);
    }

    private function runInDirectory(callable $callable, string $directoryOfExecution): void
    {
        $originalDirectory = Env\current_dir();

        try {
            Env\set_current_dir($directoryOfExecution);
            $callable();
        } finally {
            Env\set_current_dir($originalDirectory);
        }
    }
}
