<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\MethodBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\ExcludeInternalMethod;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\MethodBased;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

use function assert;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\MethodBased\ExcludeInternalMethod */
final class ExcludeInternalMethodTest extends TestCase
{
    public function testNormalMethodsAreNotExcluded(): void
    {
        $method = (new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
	/** @api */
    function method() {}
}
PHP
            ,
            (new BetterReflection())->astLocator(),
        )))
            ->reflectClass('A')
            ->getMethod('method');

        assert($method !== null);

        $check = $this->createMock(MethodBased::class);
        $check->expects(self::once())
              ->method('__invoke')
              ->with($method, $method)
              ->willReturn(Changes::fromList(Change::removed('foo', true)));

        self::assertEquals(
            Changes::fromList(Change::removed('foo', true)),
            (new ExcludeInternalMethod($check))($method, $method),
        );
    }

    public function testInternalFunctionsAreExcluded(): void
    {
        $method = (new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
    function method() {}
}
PHP
            ,
            (new BetterReflection())->astLocator(),
        )))
            ->reflectClass('A')
            ->getMethod('method');
        
        assert($method !== null);

        $check = $this->createMock(MethodBased::class);
        $check->expects(self::never())
              ->method('__invoke');

        self::assertEquals(
            Changes::empty(),
            (new ExcludeInternalMethod($check))($method, $method),
        );
    }
}
