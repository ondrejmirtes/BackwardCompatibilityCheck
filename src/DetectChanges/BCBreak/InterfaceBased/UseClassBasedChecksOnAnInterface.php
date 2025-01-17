<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBased;
use PHPStan\BetterReflection\Reflection\ReflectionClass;

final class UseClassBasedChecksOnAnInterface implements InterfaceBased
{
    public function __construct(private ClassBased $check)
    {
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        return ($this->check)($fromInterface, $toInterface);
    }
}
