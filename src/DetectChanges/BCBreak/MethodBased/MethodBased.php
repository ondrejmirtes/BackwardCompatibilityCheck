<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use Roave\BackwardCompatibility\Changes;

interface MethodBased
{
    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes;
}
