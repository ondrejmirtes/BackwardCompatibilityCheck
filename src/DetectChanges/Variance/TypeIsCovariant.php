<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\Variance;

use Psl\Iter;
use Psl\Str;
use Roave\BetterReflection\Reflection\ReflectionIntersectionType;
use Roave\BetterReflection\Reflection\ReflectionNamedType;
use Roave\BetterReflection\Reflection\ReflectionUnionType;

/**
 * This is a simplistic covariant type check. A more appropriate approach would be to
 * have a `$type->includes($otherType)` check with actual types represented as value objects,
 * but that is a massive piece of work that should be done by importing an external library
 * instead, if this class no longer suffices.
 */
final class TypeIsCovariant
{
    public function __invoke(
        ReflectionIntersectionType|ReflectionUnionType|ReflectionNamedType|null $type,
        ReflectionIntersectionType|ReflectionUnionType|ReflectionNamedType|null $comparedType,
    ): bool {
        if ($type === null) {
            // everything can be covariant to `mixed`
            return true;
        }

        if ($comparedType === null) {
            // widening a type is not covariant
            return false;
        }

        if ($type instanceof ReflectionIntersectionType) {
            return Iter\all(
                $type->getTypes(),
                fn (ReflectionNamedType $type): bool => $this($type, $comparedType)
            );
        }

        if ($comparedType instanceof ReflectionIntersectionType) {
            return Iter\any(
                $comparedType->getTypes(),
                fn (ReflectionNamedType $comparedType): bool => $this($type, $comparedType)
            );
        }

        if ($comparedType instanceof ReflectionUnionType) {
            return Iter\all(
                $comparedType->getTypes(),
                fn (ReflectionNamedType $comparedType): bool => $this($type, $comparedType)
            );
        }

        if ($type instanceof ReflectionUnionType) {
            return Iter\any(
                $type->getTypes(),
                fn (ReflectionNamedType $type): bool => $this($type, $comparedType)
            );
        }

        return $this->compareNamedTypes($type, $comparedType);
    }

    private function compareNamedTypes(ReflectionNamedType $type, ReflectionNamedType $comparedType): bool
    {
        $typeAsString         = $type->getName();
        $comparedTypeAsString = $comparedType->getName();

        return Str\lowercase($typeAsString) === Str\lowercase($comparedTypeAsString);
    }
}
