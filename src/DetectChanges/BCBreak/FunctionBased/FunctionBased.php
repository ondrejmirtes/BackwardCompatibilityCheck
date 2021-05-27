<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased;

use PHPStan\BetterReflection\Reflection\ReflectionFunctionAbstract;
use Roave\BackwardCompatibility\Changes;

interface FunctionBased
{
    public function __invoke(ReflectionFunctionAbstract $fromFunction, ReflectionFunctionAbstract $toFunction): Changes;
}
