<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Regex;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;
use PHPStan\BetterReflection\Reflection\ReflectionClass;

/**
 * Classes marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalClass implements ClassBased
{
    public function __construct(private ClassBased $check)
    {
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if (InternalHelper::isClassInternal($fromClass)) {
            return Changes::empty();
        }

        return ($this->check)($fromClass, $toClass);
    }
}
