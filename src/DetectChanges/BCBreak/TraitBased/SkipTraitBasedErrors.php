<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipTraitBasedErrors implements TraitBased
{
    private TraitBased $next;

    public function __construct(TraitBased $next)
    {
        $this->next = $next;
    }

    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes
    {
        try {
            return $this->next->__invoke($fromTrait, $toTrait);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
