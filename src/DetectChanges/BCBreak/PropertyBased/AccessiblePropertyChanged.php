<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;

final class AccessiblePropertyChanged implements PropertyBased
{
    public function __construct(private PropertyBased $propertyBased)
    {
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        if ($fromProperty->isPrivate()) {
            return Changes::empty();
        }

        return ($this->propertyBased)($fromProperty, $toProperty);
    }
}
