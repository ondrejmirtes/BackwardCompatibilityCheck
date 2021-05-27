<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use Psl\Regex;
use Roave\BackwardCompatibility\Changes;

final class ExcludeInternalProperty implements PropertyBased
{
    private PropertyBased $propertyBased;

    public function __construct(PropertyBased $propertyBased)
    {
        $this->propertyBased = $propertyBased;
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        if ($this->isInternalDocComment($fromProperty->getDocComment())) {
            return Changes::empty();
        }

        return $this->propertyBased->__invoke($fromProperty, $toProperty);
    }

    private function isInternalDocComment(string $comment): bool
    {
        return Regex\matches($comment, '/\s+@internal\s+/');
    }
}
