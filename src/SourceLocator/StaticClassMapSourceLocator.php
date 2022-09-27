<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\SourceLocator;

use Psl;
use Psl\Dict;
use Psl\Filesystem;
use Psl\Iter;
use Psl\Type;
use PHPStan\BetterReflection\Identifier\Identifier;
use PHPStan\BetterReflection\SourceLocator\Ast\Locator;
use PHPStan\BetterReflection\SourceLocator\Located\LocatedSource;
use PHPStan\BetterReflection\SourceLocator\Type\AbstractSourceLocator;

final class StaticClassMapSourceLocator extends AbstractSourceLocator
{
    /** @var array<string, string> */
    private array $classMap;

     /**
      * @param array<string, string> $classMap map of class => file. Every file must exist,
      *                                        every key must be non-empty
      */
    public function __construct(
        array $classMap,
        Locator $astLocator,
    ) {
        parent::__construct($astLocator);

        $realPaths = Dict\map($classMap, static function (string $file): string {
            return Type\string()->assert(Filesystem\canonicalize($file));
        });

        Psl\invariant(Iter\all($realPaths, static function (string $file): bool {
            return Filesystem\is_file($file);
        }), 'Invalid class-map.');

        $this->classMap = Type\dict(Type\non_empty_string(), Type\string())->coerce($realPaths);
    }

    protected function createLocatedSource(Identifier $identifier): LocatedSource|null
    {
        if (! $identifier->isClass()) {
            return null;
        }

        $classFile = $this->classMap[$identifier->getName()] ?? null;

        if ($classFile === null) {
            return null;
        }

        return new LocatedSource(Filesystem\read_file($classFile), $identifier->getName(), $classFile);
    }
}
