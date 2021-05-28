<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;

/**
 * Traits marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalTrait implements TraitBased
{
    private TraitBased $check;

    public function __construct(TraitBased $check)
    {
        $this->check = $check;
    }

    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes
    {
        if (InternalHelper::isTraitInternal($fromTrait)) {
            return Changes::empty();
        }

        return $this->check->__invoke($fromTrait, $toTrait);
    }
}
