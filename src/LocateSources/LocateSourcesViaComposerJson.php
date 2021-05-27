<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\LocateSources;

use PHPStan\BetterReflection\SourceLocator\Ast\Locator;
use PHPStan\BetterReflection\SourceLocator\Type\Composer\Factory\MakeLocatorForComposerJson;
use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

final class LocateSourcesViaComposerJson implements LocateSources
{
    private Locator $astLocator;

    public function __construct(Locator $astLocator)
    {
        $this->astLocator = $astLocator;
    }

    public function __invoke(string $installationPath): SourceLocator
    {
        return (new MakeLocatorForComposerJson())
            ->__invoke($installationPath, $this->astLocator);
    }
}
