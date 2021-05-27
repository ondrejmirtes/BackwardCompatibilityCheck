<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;

final class MultipleChecksOnAProperty implements PropertyBased
{
    /** @var PropertyBased[] */
    private array $checks;

    public function __construct(PropertyBased ...$checks)
    {
        $this->checks = $checks;
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        return Changes::fromIterator($this->multipleChecks($fromProperty, $toProperty));
    }

    /** @return iterable|Change[] */
    private function multipleChecks(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): iterable
    {
        foreach ($this->checks as $check) {
            yield from $check->__invoke($fromProperty, $toProperty);
        }
    }
}
