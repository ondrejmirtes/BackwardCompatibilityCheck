<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\Factory;

use Roave\BackwardCompatibility\LocateSources\LocateSources;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\Reflector\Reflector;
use PHPStan\BetterReflection\SourceLocator\Exception\InvalidDirectory;
use PHPStan\BetterReflection\SourceLocator\Exception\InvalidFileInfo;
use PHPStan\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\MemoizingSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

final class ComposerInstallationReflectorFactory
{
    public function __construct(private LocateSources $locateSources)
    {
    }

    /**
     * @throws InvalidFileInfo
     * @throws InvalidDirectory
     */
    public function __invoke(
        string $installationDirectory,
        SourceLocator $dependencies,
    ): Reflector {
        return new DefaultReflector(
            new MemoizingSourceLocator(new AggregateSourceLocator([
                ($this->locateSources)($installationDirectory),
                $dependencies,
            ])),
        );
    }
}
