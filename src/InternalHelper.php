<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility;

use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;
use Roave\BetterReflection\Reflection\ReflectionProperty;

use function str_contains;

/**
 * Symbols are internal unless marked with `@api`, according to
 * https://phpstan.org/developing-extensions/backward-compatibility-promise
 */
final class InternalHelper
{
    public static function isClassInternal(ReflectionClass $class): bool
    {
        if (self::isApi($class->getDocComment())) {
            return false;
        }

        foreach ($class->getImmediateMethods() as $method) {
            if (self::isApi($method->getDocComment())) {
                return false;
            }
        }

        return true;
    }

    public static function isMethodInternal(ReflectionMethod $method): bool
    {
        if (self::isApi($method->getDocComment())) {
            return false;
        }

        if ($method->isConstructor()) {
            return true;
        }

        return ! self::isApi($method->getImplementingClass()->getDocComment());
    }

    public static function isPropertyInternal(ReflectionProperty $property): bool
    {
        if (self::isApi($property->getDocComment())) {
            return false;
        }

        return ! self::isApi($property->getImplementingClass()->getDocComment());
    }

    public static function isTraitInternal(ReflectionClass $trait): bool
    {
        return ! self::isApi($trait->getDocComment());
    }

    public static function isInterfaceInternal(ReflectionClass $interface): bool
    {
        return ! self::isApi($interface->getDocComment());
    }

    private static function isApi(string|null $docComment): bool
    {
        return $docComment !== null && str_contains($docComment, '@api');
    }
}
