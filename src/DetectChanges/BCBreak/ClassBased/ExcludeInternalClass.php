<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;

/**
 * Classes marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalClass implements ClassBased
{
    private ClassBased $check;

    public function __construct(ClassBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if (InternalHelper::isClassInternal($fromClass)) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromClass, $toClass);
    }
}
