<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;

/**
 * Methods marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalMethod implements MethodBased
{
    private MethodBased $check;

    public function __construct(MethodBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        if (InternalHelper::isMethodInternal($fromMethod)) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromMethod, $toMethod);
    }
}
