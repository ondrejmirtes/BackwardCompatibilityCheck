<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBased;

final class UseClassBasedChecksOnATrait implements TraitBased
{
    private ClassBased $check;

    public function __construct(ClassBased $classBased)
    {
        $this->check = $classBased;
    }

    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes
    {
        return $this->check->__invoke($fromTrait, $toTrait);
    }
}
