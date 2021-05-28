<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility;

use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflection\ReflectionMethod;
use PHPStan\BetterReflection\Reflection\ReflectionProperty;

use function strpos;

class InternalHelper
{
    public static function isClassInternal(ReflectionClass $class): bool
    {
        $docComment = $class->getDocComment();
        if (strpos($docComment, '@api') !== false) {
            return false;
        }

        foreach ($class->getImmediateMethods() as $method) {
            $methodDocComment = $method->getDocComment();
            if (strpos($methodDocComment, '@api') !== false) {
                return false;
            }
        }

        return true;
    }

    public static function isMethodInternal(ReflectionMethod $method): bool
    {
        $docComment = $method->getDocComment();
        if (strpos($docComment, '@api') !== false) {
            return false;
        }

        if ($method->isConstructor()) {
            return true;
        }

        $declaringClass  = $method->getImplementingClass();
        $classDocComment = $declaringClass->getDocComment();

        return strpos($classDocComment, '@api') === false;
    }

    public static function isPropertyInternal(ReflectionProperty $property): bool
    {
        $docComment = $property->getDocComment();
        if (strpos($docComment, '@api') !== false) {
            return false;
        }

        $declaringClass  = $property->getImplementingClass();
        $classDocComment = $declaringClass->getDocComment();

        return strpos($classDocComment, '@api') === false;
    }

    public static function isTraitInternal(ReflectionClass $trait): bool
    {
        $docComment = $trait->getDocComment();

        return strpos($docComment, '@api') === false;
    }

    public static function isInterfaceInternal(ReflectionClass $interface): bool
    {
        $docComment = $interface->getDocComment();

        return strpos($docComment, '@api') === false;
    }
}
