<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

/**
 * A method that changes from non-final to final breaks all child classes that
 * override it.
 */
final class MethodBecameFinal implements MethodBased
{
    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        if ($fromMethod->isFinal() || ! $toMethod->isFinal()) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            Str\format(
                'Method %s() of class %s became final',
                $fromMethod->getName(),
                $fromMethod->getDeclaringClass()->getName(),
            ),
        ));
    }
}
