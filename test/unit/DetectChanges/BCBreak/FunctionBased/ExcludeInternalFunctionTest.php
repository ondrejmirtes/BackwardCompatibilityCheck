<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased;

use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\Reflector\FunctionReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;
use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased\ExcludeInternalFunction;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased\FunctionBased;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\FunctionBased\ExcludeInternalFunction */
final class ExcludeInternalFunctionTest extends TestCase
{
    public function testNormalFunctionsAreNotExcluded(): void
    {
        $source   = new StringSourceLocator(
            <<<'PHP'
<?php

function a() {}
PHP
            ,
            (new BetterReflection())->astLocator()
        );
        $function = (new FunctionReflector($source, new ClassReflector($source)))
            ->reflect('a');

        $check = $this->createMock(FunctionBased::class);
        $check->expects(self::once())
              ->method('__invoke')
              ->with($function, $function)
              ->willReturn(Changes::fromList(Change::removed('foo', true)));

        self::assertEquals(
            Changes::fromList(Change::removed('foo', true)),
            (new ExcludeInternalFunction($check))
                ->__invoke($function, $function)
        );
    }

    public function testInternalFunctionsAreExcluded(): void
    {
        $source   = new StringSourceLocator(
            <<<'PHP'
<?php

/** @internal */
function a() {}
PHP
            ,
            (new BetterReflection())->astLocator()
        );
        $function = (new FunctionReflector($source, new ClassReflector($source)))
            ->reflect('a');

        $check = $this->createMock(FunctionBased::class);
        $check->expects(self::never())
              ->method('__invoke');

        self::assertEquals(
            Changes::empty(),
            (new ExcludeInternalFunction($check))
                ->__invoke($function, $function)
        );
    }
}
