<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility;

use Psl\Dict;
use Psl\Str;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBased;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased\InterfaceBased;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\TraitBased\TraitBased;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflector\Exception\IdentifierNotFound;
use PHPStan\BetterReflection\Reflector\Reflector;

final class CompareClasses implements CompareApi
{
    public function __construct(
        private ClassBased $classBasedComparisons,
        private InterfaceBased $interfaceBasedComparisons,
        private TraitBased $traitBasedComparisons,
    ) {
    }

    public function __invoke(
        Reflector $definedSymbols,
        Reflector $pastSourcesWithDependencies,
        Reflector $newSourcesWithDependencies,
    ): Changes {
        $definedApiClassNames = Dict\map(
            Dict\filter(
                $definedSymbols->reflectAllClasses(),
                static function (ReflectionClass $class): bool {
                    return ! ($class->isAnonymous() || InternalHelper::isClassInternal($class));
                }
            ),
            static function (ReflectionClass $class): string {
                return $class->getName();
            },
        );

        return Changes::fromIterator($this->makeSymbolsIterator(
            $definedApiClassNames,
            $pastSourcesWithDependencies,
            $newSourcesWithDependencies,
        ));
    }

    /**
     * @param string[] $definedApiClassNames
     *
     * @return iterable<int, Change>
     */
    private function makeSymbolsIterator(
        array $definedApiClassNames,
        Reflector $pastSourcesWithDependencies,
        Reflector $newSourcesWithDependencies,
    ): iterable {
        foreach ($definedApiClassNames as $apiClassName) {
            $oldSymbol = $pastSourcesWithDependencies->reflectClass($apiClassName);

            yield from $this->examineSymbol($oldSymbol, $newSourcesWithDependencies);
        }
    }

    /** @return iterable<int, Change> */
    private function examineSymbol(
        ReflectionClass $oldSymbol,
        Reflector $newSourcesWithDependencies,
    ): iterable {
        try {
            $newClass = $newSourcesWithDependencies->reflectClass($oldSymbol->getName());
        } catch (IdentifierNotFound) {
            yield Change::removed(Str\format('Class %s has been deleted', $oldSymbol->getName()));

            return;
        }

        if ($oldSymbol->isInterface()) {
            yield from ($this->interfaceBasedComparisons)($oldSymbol, $newClass);

            return;
        }

        if ($oldSymbol->isTrait()) {
            yield from ($this->traitBasedComparisons)($oldSymbol, $newClass);

            return;
        }

        yield from ($this->classBasedComparisons)($oldSymbol, $newClass);
    }
}
