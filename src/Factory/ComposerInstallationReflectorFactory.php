<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\Factory;

use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\SourceLocator\Exception\InvalidDirectory;
use PHPStan\BetterReflection\SourceLocator\Exception\InvalidFileInfo;
use PHPStan\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;
use Roave\BackwardCompatibility\LocateSources\LocateSources;

/**
 * @codeCoverageIgnore
 */
final class ComposerInstallationReflectorFactory
{
    private LocateSources $locateSources;

    public function __construct(LocateSources $locateSources)
    {
        $this->locateSources = $locateSources;
    }

    /**
     * @throws InvalidFileInfo
     * @throws InvalidDirectory
     */
    public function __invoke(
        string $installationDirectory,
        SourceLocator $dependencies
    ): ClassReflector {
        return new ClassReflector(
            new MemoizingSourceLocator(new AggregateSourceLocator([
                $this->locateSources->__invoke($installationDirectory),
                $dependencies,
            ]))
        );
    }
}
