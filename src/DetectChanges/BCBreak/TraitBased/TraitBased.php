<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Roave\BackwardCompatibility\Changes;

interface TraitBased
{
    public function __invoke(ReflectionClass $fromTrait, ReflectionClass $toTrait): Changes;
}
