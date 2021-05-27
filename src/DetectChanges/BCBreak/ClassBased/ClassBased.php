<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;

interface ClassBased
{
    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes;
}
