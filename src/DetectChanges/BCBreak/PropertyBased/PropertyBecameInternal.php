<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\Formatter\ReflectionPropertyName;
use Roave\BackwardCompatibility\InternalHelper;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;

/**
 * A property that is marked internal is no available to downstream consumers.
 */
final class PropertyBecameInternal implements PropertyBased
{
    private ReflectionPropertyName $formatProperty;

    public function __construct()
    {
        $this->formatProperty = new ReflectionPropertyName();
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        if (
            InternalHelper::isPropertyInternal($toProperty)
            && ! InternalHelper::isPropertyInternal($fromProperty)
        ) {
            return Changes::fromList(Change::changed(
                Str\format(
                    'Property %s was marked "@internal"',
                    ($this->formatProperty)($fromProperty),
                ),
            ));
        }

        return Changes::empty();
    }
}
