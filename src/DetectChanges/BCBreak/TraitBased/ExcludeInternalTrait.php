<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * Traits marked "internal" (docblock) are not affected by BC checks.
 */
final class ExcludeInternalTrait implements TraitBased
{
    public function __construct(private TraitBased $check)
    {
    }

    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes
    {
        if (InternalHelper::isTraitInternal($fromTrait)) {
            return Changes::empty();
        }

        return ($this->check)($fromTrait, $toTrait);
    }
}
