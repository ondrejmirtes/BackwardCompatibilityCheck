<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\Variance;

use Psl\Iter;
use Psl\Str;
use PHPStan\BetterReflection\Reflection\ReflectionIntersectionType;
use PHPStan\BetterReflection\Reflection\ReflectionNamedType;
use PHPStan\BetterReflection\Reflection\ReflectionUnionType;

/**
 * This is a simplistic contravariant type check. A more appropriate approach would be to
 * have a `$type->includes($otherType)` check with actual types represented as value objects,
 * but that is a massive piece of work that should be done by importing an external library
 * instead, if this class no longer suffices.
 */
final class TypeIsContravariant
{
    public function __invoke(
        ReflectionIntersectionType|ReflectionUnionType|ReflectionNamedType|null $type,
        ReflectionIntersectionType|ReflectionUnionType|ReflectionNamedType|null $comparedType,
    ): bool {
        if (
            ($type && $type->__toString() === 'never')
            || ($comparedType && $comparedType->__toString() === 'never')
        ) {
            return false;
        }

        if ($comparedType === null || $comparedType->__toString() === 'mixed') {
            return true;
        }

        if ($type === null) {
            // nothing can be contravariant to `mixed` besides `mixed` itself (handled above)
            return false;
        }

        if ($type instanceof ReflectionUnionType) {
            return Iter\all(
                $type->getTypes(),
                fn (ReflectionNamedType|ReflectionIntersectionType $type): bool => $this($type, $comparedType)
            );
        }

        if ($comparedType instanceof ReflectionUnionType) {
            return Iter\any(
                $comparedType->getTypes(),
                fn (ReflectionNamedType|ReflectionIntersectionType $comparedType): bool => $this($type, $comparedType)
            );
        }

        if ($comparedType instanceof ReflectionIntersectionType) {
            return Iter\all(
                $comparedType->getTypes(),
                fn (ReflectionNamedType $comparedType): bool => $this($type, $comparedType)
            );
        }

        if ($type instanceof ReflectionIntersectionType) {
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
