<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;
use PHPStan\BetterReflection\Reflection\ReflectionClass;

/**
 * Interfaces marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalInterface implements InterfaceBased
{
    public function __construct(private InterfaceBased $check)
    {
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        if (InternalHelper::isInterfaceInternal($fromInterface)) {
            return Changes::empty();
        }

        return ($this->check)($fromInterface, $toInterface);
    }
}
