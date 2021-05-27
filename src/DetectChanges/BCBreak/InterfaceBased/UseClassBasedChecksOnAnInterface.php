<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBased;

final class UseClassBasedChecksOnAnInterface implements InterfaceBased
{
    private ClassBased $check;

    public function __construct(ClassBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        return $this->check->__invoke($fromInterface, $toInterface);
    }
}
