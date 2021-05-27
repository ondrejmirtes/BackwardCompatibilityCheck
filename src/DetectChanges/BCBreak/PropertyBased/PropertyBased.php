<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use Roave\BackwardCompatibility\Changes;

interface PropertyBased
{
    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes;
}
