<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;

/**
 * A class that is marked internal is no available to downstream consumers.
 */
final class ClassBecameInternal implements ClassBased
{
    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if (
            ! InternalHelper::isClassInternal($fromClass)
            && InternalHelper::isClassInternal($toClass)
        ) {
            return Changes::fromList(Change::changed(
                Str\format(
                    '%s was marked "@internal"',
                    $fromClass->getName()
                ),
                true
            ));
        }

        return Changes::empty();
    }
}
