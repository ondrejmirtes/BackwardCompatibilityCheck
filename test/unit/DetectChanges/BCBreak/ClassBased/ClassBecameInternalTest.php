<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBecameInternal;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

use function array_map;
use function iterator_to_array;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBecameInternal */
final class ClassBecameInternalTest extends TestCase
{
    /**
     * @param string[] $expectedMessages
     *
     * @dataProvider classesToBeTested
     */
    public function testDiffs(
        ReflectionClass $fromClass,
        ReflectionClass $toClass,
        array $expectedMessages,
    ): void {
        $changes = (new ClassBecameInternal())($fromClass, $toClass);

        self::assertSame(
            $expectedMessages,
            array_map(static function (Change $change): string {
                return $change->__toString();
            }, iterator_to_array($changes)),
        );
    }

    /**
     * @return array<string, array<int, ReflectionClass|array<int, string>>>
     * @psalm-return array<string, array{0: ReflectionClass, 1: ReflectionClass, 2: list<string>}>
     */
    public function classesToBeTested(): array
    {
        $locator       = (new BetterReflection())->astLocator();
        $fromReflector = new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

/** @api */
class A {}
/** @api */
class B {}
class C {}
class D {}
PHP
            ,
            $locator,
        ));
        $toReflector   = new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

/** @api */
class A {}
class B {}
class C {}

/** @api */
class D {}
PHP
            ,
            $locator,
        ));

        return [
            'A' => [
                $fromReflector->reflectClass('A'),
                $toReflector->reflectClass('A'),
                [],
            ],
            'B' => [
                $fromReflector->reflectClass('B'),
                $toReflector->reflectClass('B'),
                ['[BC] CHANGED: B was marked "@internal"'],
            ],
            'C' => [
                $fromReflector->reflectClass('C'),
                $toReflector->reflectClass('C'),
                [],
            ],
            'D' => [
                $fromReflector->reflectClass('D'),
                $toReflector->reflectClass('D'),
                [],
            ],
        ];
    }
}
