<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Dict;
use Psl\Str;
use Psl\Vec;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\Formatter\FunctionName;
use Roave\BackwardCompatibility\InternalHelper;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Roave\BetterReflection\Reflection\ReflectionMethod;

final class MethodRemoved implements ClassBased
{
    private FunctionName $formatFunction;

    public function __construct()
    {
        $this->formatFunction = new FunctionName();
    }

    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        $removedMethods = Dict\diff_by_key(
            Dict\map_keys($this->accessibleMethods($fromClass), static function (string $key): string {
                return Str\uppercase($key);
            }),
            Dict\map_keys($this->accessibleMethods($toClass), static function (string $key): string {
                return Str\uppercase($key);
            }),
        );

        return Changes::fromList(...Vec\map($removedMethods, function (ReflectionMethod $method): Change {
            return Change::removed(
                Str\format('Method %s was removed', ($this->formatFunction)($method)),
            );
        }));
    }

    /** @return array<string, ReflectionMethod> */
    private function accessibleMethods(ReflectionClass $class): array
    {
        $methods = Vec\filter($class->getMethods(), static function (ReflectionMethod $method): bool {
            return ($method->isPublic()
                || $method->isProtected())
                && ! InternalHelper::isMethodInternal($method);
        });

        return Dict\associate(
            Vec\map($methods, static function (ReflectionMethod $method): string {
                return $method->getName();
            }),
            $methods,
        );
    }
}
