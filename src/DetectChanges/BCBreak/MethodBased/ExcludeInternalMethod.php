<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use Psl\Regex;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

/**
 * Methods marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalMethod implements MethodBased
{
    public function __construct(private MethodBased $check)
    {
    }

    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        if (InternalHelper::isMethodInternal($fromMethod)) {
            return Changes::empty();
        }

        return ($this->check)($fromMethod, $toMethod);
    }
}
