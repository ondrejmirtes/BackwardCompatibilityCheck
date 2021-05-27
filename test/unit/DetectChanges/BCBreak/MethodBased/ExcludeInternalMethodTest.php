<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;
use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\ExcludeInternalMethod;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\MethodBased;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\ExcludeInternalMethod */
final class ExcludeInternalMethodTest extends TestCase
{
    public function testNormalMethodsAreNotExcluded(): void
    {
        $method = (new ClassReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
    function method() {}
}
PHP
            ,
            (new BetterReflection())->astLocator()
        )))
            ->reflect('A')
            ->getMethod('method');

        $check = $this->createMock(MethodBased::class);
        $check->expects(self::once())
              ->method('__invoke')
              ->with($method, $method)
              ->willReturn(Changes::fromList(Change::removed('foo', true)));

        self::assertEquals(
            Changes::fromList(Change::removed('foo', true)),
            (new ExcludeInternalMethod($check))
                ->__invoke($method, $method)
        );
    }

    public function testInternalFunctionsAreExcluded(): void
    {
        $method = (new ClassReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
    /** @internal */
    function method() {}
}
PHP
            ,
            (new BetterReflection())->astLocator()
        )))
            ->reflect('A')
            ->getMethod('method');

        $check = $this->createMock(MethodBased::class);
        $check->expects(self::never())
              ->method('__invoke');

        self::assertEquals(
            Changes::empty(),
            (new ExcludeInternalMethod($check))
                ->__invoke($method, $method)
        );
    }
}
