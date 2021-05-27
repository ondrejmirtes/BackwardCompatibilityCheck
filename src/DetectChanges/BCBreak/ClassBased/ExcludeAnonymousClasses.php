<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;

final class ExcludeAnonymousClasses implements ClassBased
{
    private ClassBased $check;

    public function __construct(ClassBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if ($fromClass->isAnonymous()) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromClass, $toClass);
    }
}
