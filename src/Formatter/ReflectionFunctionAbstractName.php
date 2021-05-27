<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\Formatter;

use PHPStan\BetterReflection\Reflection\ReflectionFunctionAbstract;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;

final class ReflectionFunctionAbstractName
{
    public function __invoke(ReflectionFunctionAbstract $function): string
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
