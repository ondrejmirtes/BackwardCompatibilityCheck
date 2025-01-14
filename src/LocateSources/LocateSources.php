<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\LocateSources;

use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

interface LocateSources
{
    public function __invoke(string $installationPath): SourceLocator;
}
