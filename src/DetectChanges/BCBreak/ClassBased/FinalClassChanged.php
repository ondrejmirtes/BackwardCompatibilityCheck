<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;

final class FinalClassChanged implements ClassBased
{
    private ClassBased $checkClass;

    public function __construct(ClassBased $checkClass)
    {
        $this->checkClass = $checkClass;
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if (! $fromClass->isFinal()) {
            return Changes::empty();
        }

        return $this->checkClass->__invoke($fromClass, $toClass);
    }
}
