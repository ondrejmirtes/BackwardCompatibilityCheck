<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

/**
 * A method that changes from instance to static or the opposite has to be called differently,
 * so any of such changes are to be considered BC breaks
 */
final class MethodScopeChanged implements MethodBased
{
    public function __invoke(ReflectionMethod $fromMethod, ReflectionMethod $toMethod): Changes
    {
        $scopeFrom = $this->methodScope($fromMethod);
        $scopeTo   = $this->methodScope($toMethod);

        if ($scopeFrom === $scopeTo) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            Str\format(
                'Method %s() of class %s changed scope from %s to %s',
                $fromMethod->getName(),
                $fromMethod->getDeclaringClass()->getName(),
                $scopeFrom,
                $scopeTo,
            ),
        ));
    }

    private function methodScope(ReflectionMethod $method): string
    {
        return $method->isStatic() ? 'static' : 'instance';
    }
}
