<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased\ParameterByReferenceChanged;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionFunctionAbstract;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\Reflector\FunctionReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;
use function array_combine;
use function array_map;
use function iterator_to_array;

/**
 * @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased\ParameterByReferenceChanged
 */
final class ParameterByReferenceChangedTest extends TestCase
{
    /**
     * @dataProvider functionsToBeTested
     *
     * @param string[] $expectedMessages
     */
    public function testDiffs(
        ReflectionFunctionAbstract $fromFunction,
        ReflectionFunctionAbstract $toFunction,
        array $expectedMessages
    ) : void {
        $changes = (new ParameterByReferenceChanged())
            ->__invoke($fromFunction, $toFunction);

        self::assertSame(
            $expectedMessages,
            array_map(function (Change $change) : string {
                return $change->__toString();
            }, iterator_to_array($changes))
        );
    }

    /**
     * @return array<string, array<int, ReflectionFunctionAbstract|array<int, string>>>
     *
     * @psalm-return array<string, array{0: ReflectionFunctionAbstract, 1: ReflectionFunctionAbstract, 2: list<string>}>
     */
    public function functionsToBeTested() : array
    {
        $astLocator = (new BetterReflection())->astLocator();

        $fromLocator = new StringSourceLocator(
            <<<'PHP'
<?php

namespace {
    function valueToReference($a) {}
    function referenceToValue(& $a) {}
    function valueToValue($a) {}
    function referenceToReference(& $a) {}
    function referenceOnRemovedParameter($a, & $b) {}
    function referenceToValueOnRenamedParameter(& $a, & $b) {}
    function addedParameter(& $a, & $b) {}
}
namespace N1 {
    class C {
        static function changed1($a) {}
        function changed2($a) {}
    }
}
PHP
            ,
            $astLocator
        );

        $toLocator = new StringSourceLocator(
            <<<'PHP'
<?php

namespace {
    function valueToReference(& $a) {}
    function referenceToValue($a) {}
    function valueToValue($a) {}
    function referenceToReference(& $a) {}
    function referenceOnRemovedParameter($a) {}
    function referenceToValueOnRenamedParameter(& $b, $a) {}
    function addedParameter(& $a, & $b, $c) {}
}
namespace N1 {
    class C {
        static function changed1(& $a) {}
        function changed2(& $a) {}
    }
}
PHP
            ,
            $astLocator
        );

        $fromClassReflector = new ClassReflector($fromLocator);
        $toClassReflector   = new ClassReflector($toLocator);
        $fromReflector      = new FunctionReflector($fromLocator, $fromClassReflector);
        $toReflector        = new FunctionReflector($toLocator, $toClassReflector);

        $functions = [
            'valueToReference'                   => [
                '[BC] CHANGED: The parameter $a of valueToReference() changed from by-value to by-reference',
            ],
            'referenceToValue'                   => [
                '[BC] CHANGED: The parameter $a of referenceToValue() changed from by-reference to by-value',
            ],
            'valueToValue'                       => [],
            'referenceToReference'               => [],
            'referenceOnRemovedParameter'        => [],
            'referenceToValueOnRenamedParameter' => [
                '[BC] CHANGED: The parameter $b of referenceToValueOnRenamedParameter() changed from by-reference to by-value',
            ],
            'addedParameter'                     => [],
        ];

        return array_merge(
            array_combine(
                array_keys($functions),
                array_map(
                    /** @psalm-param list<string> $errorMessages https://github.com/vimeo/psalm/issues/2772 */
                    static function (string $function, array $errorMessages) use ($fromReflector, $toReflector) : array {
                        return [
                            $fromReflector->reflect($function),
                            $toReflector->reflect($function),
                            $errorMessages,
                        ];
                    },
                    array_keys($functions),
                    $functions
                )
            ),
            [
                'N1\C::changed1' => [
                    $fromClassReflector->reflect('N1\C')->getMethod('changed1'),
                    $toClassReflector->reflect('N1\C')->getMethod('changed1'),
                    [
                        '[BC] CHANGED: The parameter $a of N1\C::changed1() changed from by-value to by-reference',

                    ],
                ],
                'N1\C#changed2'  => [
                    $fromClassReflector->reflect('N1\C')->getMethod('changed2'),
                    $toClassReflector->reflect('N1\C')->getMethod('changed2'),
                    [
                        '[BC] CHANGED: The parameter $a of N1\C#changed2() changed from by-value to by-reference',
                    ],
                ],
            ]
        );
    }
}
