<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Dict;
use Psl\Vec;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased\PropertyBased;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;

final class PropertyChanged implements ClassBased
{
    public function __construct(private PropertyBased $checkProperty)
    {
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        return Changes::fromIterator($this->checkSymbols(
            $fromClass->getProperties(),
            $toClass->getProperties(),
        ));
    }

    /**
     * @param ReflectionProperty[] $from
     * @param ReflectionProperty[] $to
     *
     * @return iterable<int, Change>
     */
    private function checkSymbols(array $from, array $to): iterable
    {
        foreach (Vec\keys(Dict\intersect_by_key($from, $to)) as $name) {
            yield from ($this->checkProperty)($from[$name], $to[$name]);
        }
    }
}
