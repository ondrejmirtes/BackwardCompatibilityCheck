<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\Formatter;

use PHPStan\BetterReflection\Reflection\ReflectionFunction;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

/** @internal */
final class FunctionName
{
    public function __invoke(ReflectionMethod|ReflectionFunction $function): string
    {
        if ($function instanceof ReflectionMethod) {
            if ($function->isStatic()) {
                return $function->getDeclaringClass()->getName() . '::' . $function->getName() . '()';
            }

            return $function->getDeclaringClass()->getName() . '#' . $function->getName() . '()';
        }

        return $function->getName() . '()';
    }
}
