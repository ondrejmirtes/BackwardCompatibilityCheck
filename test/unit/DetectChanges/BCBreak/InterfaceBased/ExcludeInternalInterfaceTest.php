<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased;

use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;
use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased\ExcludeInternalInterface;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased\InterfaceBased;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\InterfaceBased\ExcludeInternalInterface */
final class ExcludeInternalInterfaceTest extends TestCase
{
    public function testNormalInterfacesAreNotExcluded(): void
    {
        $locator    = (new BetterReflection())->astLocator();
        $reflector  = new ClassReflector(new StringSourceLocator(
            <<<'PHP'
<?php

/** @api */
interface ANormalInterface {}
PHP
            ,
            $locator
        ));
        $reflection = $reflector->reflect('ANormalInterface');

        $check = $this->createMock(InterfaceBased::class);
        $check->expects(self::once())
              ->method('__invoke')
              ->with($reflection, $reflection)
              ->willReturn(Changes::fromList(Change::removed('foo', true)));

        self::assertEquals(
            Changes::fromList(Change::removed('foo', true)),
            (new ExcludeInternalInterface($check))
                ->__invoke($reflection, $reflection)
        );
    }

    public function testInternalInterfacesAreExcluded(): void
    {
        $locator    = (new BetterReflection())->astLocator();
        $reflector  = new ClassReflector(new StringSourceLocator(
            <<<'PHP'
<?php

interface AnInternalInterface {}
PHP
            ,
            $locator
        ));
        $reflection = $reflector->reflect('AnInternalInterface');

        $check = $this->createMock(InterfaceBased::class);
        $check->expects(self::never())->method('__invoke');

        self::assertEquals(
            Changes::empty(),
            (new ExcludeInternalInterface($check))
                ->__invoke($reflection, $reflection)
        );
    }
}
