<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassConstantBased;

use PHPStan\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BackwardCompatibility\Changes;

interface ClassConstantBased
{
    public function __invoke(ReflectionClassConstant $fromConstant, ReflectionClassConstant $toConstant): Changes;
}
