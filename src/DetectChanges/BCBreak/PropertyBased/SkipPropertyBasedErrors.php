<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Throwable;

final class SkipPropertyBasedErrors implements PropertyBased
{
    private PropertyBased $next;

    public function __construct(PropertyBased $next)
    {
        $this->next = $next;
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        try {
            return $this->next->__invoke($fromProperty, $toProperty);
        } catch (Throwable $failure) {
            return Changes::fromList(Change::skippedDueToFailure($failure));
        }
    }
}
