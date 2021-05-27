<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased;

use PHPStan\BetterReflection\Reflection\ReflectionFunctionAbstract;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipFunctionBasedErrors implements FunctionBased
{
    private FunctionBased $next;

    public function __construct(FunctionBased $next)
    {
        $this->next = $next;
    }

    public function __invoke(ReflectionFunctionAbstract $fromFunction, ReflectionFunctionAbstract $toFunction): Changes
    {
        try {
            return $this->next->__invoke($fromFunction, $toFunction);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
