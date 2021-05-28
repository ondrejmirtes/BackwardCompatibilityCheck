<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;

/**
 * Interfaces marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalInterface implements InterfaceBased
{
    private InterfaceBased $check;

    public function __construct(InterfaceBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        if (InternalHelper::isInterfaceInternal($fromInterface)) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromInterface, $toInterface);
    }
}
