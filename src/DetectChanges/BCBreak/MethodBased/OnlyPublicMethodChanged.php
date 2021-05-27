<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use Roave\BackwardCompatibility\Changes;

/**
 * Performs a method BC compliance check on methods that are public
 */
final class OnlyPublicMethodChanged implements MethodBased
{
    private MethodBased $check;

    public function __construct(MethodBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        if (! $fromMethod->isPublic()) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromMethod, $toMethod);
    }
}
