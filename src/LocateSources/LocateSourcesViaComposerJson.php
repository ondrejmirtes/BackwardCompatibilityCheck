<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\LocateSources;

use Roave\BackwardCompatibility\SourceLocator\ReplaceSourcePathOfLocatedSources;
use PHPStan\BetterReflection\SourceLocator\Ast\Locator;
use PHPStan\BetterReflection\SourceLocator\Type\Composer\Factory\MakeLocatorForComposerJson;
use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

final class LocateSourcesViaComposerJson implements LocateSources
{
    public function __construct(private Locator $astLocator)
    {
    }

    public function __invoke(string $installationPath): SourceLocator
    {
        return (new MakeLocatorForComposerJson())(
            $installationPath,
            new ReplaceSourcePathOfLocatedSources(
                $this->astLocator,
                $installationPath,
            ),
        );
    }
}
