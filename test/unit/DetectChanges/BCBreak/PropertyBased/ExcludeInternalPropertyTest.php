<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased\ExcludeInternalProperty;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased\PropertyBased;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

/** @covers \Roave\BackwardCompatibility\DetectChanges\BCBreak\PropertyBased\ExcludeInternalProperty */
final class ExcludeInternalPropertyTest extends TestCase
{
    public function testNormalPropertiesAreNotExcluded(): void
    {
        $property = (new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
	/** @api */
    public $property;
}
PHP
            ,
            (new BetterReflection())->astLocator(),
        )))
            ->reflectClass('A')
            ->getProperty('property');

        self::assertNotNull($property);

        $check = $this->createMock(PropertyBased::class);
        $check->expects(self::once())
              ->method('__invoke')
              ->with($property, $property)
              ->willReturn(Changes::fromList(Change::removed('foo', true)));

        self::assertEquals(
            Changes::fromList(Change::removed('foo', true)),
            (new ExcludeInternalProperty($check))($property, $property),
        );
    }

    public function testInternalPropertiesAreExcluded(): void
    {
        $property = (new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class A {
    public $property;
}
PHP
            ,
            (new BetterReflection())->astLocator(),
        )))
            ->reflectClass('A')
            ->getProperty('property');

        self::assertNotNull($property);

        $check = $this->createMock(PropertyBased::class);
        $check->expects(self::never())
              ->method('__invoke');

        self::assertEquals(
            Changes::empty(),
            (new ExcludeInternalProperty($check))($property, $property),
        );
    }
}
