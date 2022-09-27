<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\LocateDependencies;

use PHPStan\BetterReflection\SourceLocator\Type\SourceLocator;

interface LocateDependencies
{
    public function __invoke(string $installationPath, bool $includeDevelopmentDependencies): SourceLocator;
}
