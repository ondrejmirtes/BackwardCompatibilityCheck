<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipInterfaces implements InterfaceBased
{
    /** @var string[] */
    private array $interfaceNames;

    private InterfaceBased $next;

    /** @param string[] $interfaceNames */
    public function __construct(array $interfaceNames, InterfaceBased $next)
    {
        $this->interfaceNames = $interfaceNames;
        $this->next           = $next;
    }

    public function __invoke(ReflectionClass $fromInterface, ReflectionClass $toInterface): Changes
    {
        foreach ($this->interfaceNames as $interfaceName) {
            if ($fromInterface->getName() === $interfaceName || $fromInterface->implementsInterface($interfaceName)) {
                return Changes::empty();
            }
        }

        try {
            return $this->next->__invoke($fromInterface, $toInterface);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
