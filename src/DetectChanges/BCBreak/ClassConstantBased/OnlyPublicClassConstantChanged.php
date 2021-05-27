<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassConstantBased;

use PHPStan\BetterReflection\Reflection\ReflectionClassConstant;
use Roave\BackwardCompatibility\Changes;

final class OnlyPublicClassConstantChanged implements ClassConstantBased
{
    private ClassConstantBased $constantCheck;

    public function __construct(ClassConstantBased $constantCheck)
    {
        $this->constantCheck = $constantCheck;
    }

    public function __invoke(ReflectionClassConstant $fromConstant, ReflectionClassConstant $toConstant): Changes
    {
        if (! $fromConstant->isPublic()) {
            return Changes::empty();
        }

        return $this->constantCheck->__invoke($fromConstant, $toConstant);
    }
}
