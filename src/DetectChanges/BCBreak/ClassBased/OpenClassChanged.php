<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionClass;

final class OpenClassChanged implements ClassBased
{
    public function __construct(private ClassBased $checkClass)
    {
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if ($fromClass->isFinal()) {
            return Changes::empty();
        }

        return ($this->checkClass)($fromClass, $toClass);
    }
}
