<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\SourceLocator;

use PHPStan\BetterReflection\Identifier\Identifier;
use PHPStan\BetterReflection\Identifier\IdentifierType;
use PHPStan\BetterReflection\Reflection\Reflection;
use PHPStan\BetterReflection\Reflector\Reflector;
use PHPStan\BetterReflection\SourceLocator\Ast\Locator;
use PHPStan\BetterReflection\SourceLocator\Located\LocatedSource;

/** @internal */
final class ReplaceSourcePathOfLocatedSources extends Locator
{
    public function __construct(
        private Locator $next,
        private string $sourcesDirectory,
    ) {
    }

    /** {@inheritDoc} */
    public function findReflection(
        Reflector $reflector,
        LocatedSource $locatedSource,
        Identifier $identifier,
    ): Reflection {
        return $this->next->findReflection(
            $reflector,
            new LocatedSourceWithStrippedSourcesDirectory($locatedSource, $this->sourcesDirectory),
            $identifier,
        );
    }

    /** {@inheritDoc} */
    public function findReflectionsOfType(
        Reflector $reflector,
        LocatedSource $locatedSource,
        IdentifierType $identifierType,
    ): array {
        return $this->next->findReflectionsOfType(
            $reflector,
            new LocatedSourceWithStrippedSourcesDirectory($locatedSource, $this->sourcesDirectory),
            $identifierType,
        );
    }
}
