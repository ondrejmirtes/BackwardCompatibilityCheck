<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassConstantBased;

use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionClassConstant;
use Throwable;

final class SkipClassConstantBasedErrors implements ClassConstantBased
{
    public function __construct(private ClassConstantBased $next)
    {
    }

    public function __invoke(ReflectionClassConstant $fromConstant, ReflectionClassConstant $toConstant): Changes
    {
        try {
            return ($this->next)($fromConstant, $toConstant);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
