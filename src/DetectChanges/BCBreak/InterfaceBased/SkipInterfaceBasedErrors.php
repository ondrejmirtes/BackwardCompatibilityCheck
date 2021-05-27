<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipInterfaceBasedErrors implements InterfaceBased
{
    private InterfaceBased $next;

    public function __construct(InterfaceBased $next)
    {
        $this->next = $next;
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        try {
            return $this->next->__invoke($fromInterface, $toInterface);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
