<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipInterface implements InterfaceBased
{
    private string $interfaceName;

    private InterfaceBased $next;

    public function __construct(string $interfaceName, InterfaceBased $next)
    {
        $this->interfaceName = $interfaceName;
        $this->next          = $next;
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        if ($fromInterface->getName() === $this->interfaceName || $fromInterface->implementsInterface($this->interfaceName)) {
            return Changes::empty();
        }

        try {
            return $this->next->__invoke($fromInterface, $toInterface);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
