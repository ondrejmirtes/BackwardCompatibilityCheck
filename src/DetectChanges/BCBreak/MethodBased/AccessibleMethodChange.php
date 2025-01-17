<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

/**
 * Performs a method BC compliance check on methods that are visible
 */
final class AccessibleMethodChange implements MethodBased
{
    public function __construct(private MethodBased $check)
    {
    }

    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        if ($fromMethod->isPrivate()) {
            return Changes::empty();
        }

        return ($this->check)($fromMethod, $toMethod);
    }
}
