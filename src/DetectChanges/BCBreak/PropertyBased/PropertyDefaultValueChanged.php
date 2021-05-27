<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPStan\BetterReflection\Reflection\ReflectionProperty;
use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\Formatter\ReflectionPropertyName;

use function var_export;

final class PropertyDefaultValueChanged implements PropertyBased
{
    private ReflectionPropertyName $formatProperty;

    public function __construct()
    {
        $this->formatProperty = new ReflectionPropertyName();
    }

    public function __invoke(ReflectionProperty $fromProperty, ReflectionProperty $toProperty): Changes
    {
        $fromPropertyDefaultValue = $fromProperty->getDefaultValue();
        $toPropertyDefaultValue   = $toProperty->getDefaultValue();

        if ($fromPropertyDefaultValue === $toPropertyDefaultValue) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            Str\format(
                'Property %s changed default value from %s to %s',
                $this->formatProperty->__invoke($fromProperty),
                var_export($fromPropertyDefaultValue, true),
                var_export($toPropertyDefaultValue, true)
            ),
            true
        ));
    }
}
