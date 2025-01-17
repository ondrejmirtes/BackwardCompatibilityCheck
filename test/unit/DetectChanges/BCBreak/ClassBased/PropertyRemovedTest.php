<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\PropertyRemoved;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\SourceLocator\Type\SingleFileSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

use function array_map;
use function iterator_to_array;

final class PropertyRemovedTest extends TestCase
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
        $changes = (new PropertyRemoved())($fromClass, $toClass);

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
        $locator = (new BetterReflection())->astLocator();

        return [
            'RoaveTestAsset\\ClassWithPropertiesBeingRemoved' => [
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/old/ClassWithPropertiesBeingRemoved.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\\ClassWithPropertiesBeingRemoved'),
                (new DefaultReflector(new SingleFileSourceLocator(
                    __DIR__ . '/../../../../asset/api/new/ClassWithPropertiesBeingRemoved.php',
                    $locator,
                )))->reflectClass('RoaveTestAsset\\ClassWithPropertiesBeingRemoved'),
                [
                    '[BC] REMOVED: Property RoaveTestAsset\ClassWithPropertiesBeingRemoved#$removedPublicProperty was removed',
                    '[BC] REMOVED: Property RoaveTestAsset\ClassWithPropertiesBeingRemoved#$nameCaseChangePublicProperty was removed',
                    '[BC] REMOVED: Property RoaveTestAsset\ClassWithPropertiesBeingRemoved#$removedProtectedProperty was removed',
                    '[BC] REMOVED: Property RoaveTestAsset\ClassWithPropertiesBeingRemoved#$nameCaseChangeProtectedProperty was removed',
                ],
            ],
            'Decreased property visibility / removed properties in a final class - only `public` properties affect BC' => [
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

/** @api */
final class FinalClass
{
	/** @api */
    public $decreasedVisibilityPublicProperty;
    
    /** @api */
    public $removedPublicProperty;
    protected $decreasedVisibilityProtectedProperty;
    protected $removedProtectedProperty;
    private $privateProperty;
}
PHP
                    ,
                    $locator,
                )))->reflectClass('FinalClass'),
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

/** @api */
final class FinalClass
{
	/** @api */
    protected $decreasedVisibilityPublicProperty;
    private $decreasedVisibilityProtectedProperty;
}
PHP
                    ,
                    $locator,
                )))->reflectClass('FinalClass'),
                [
                    '[BC] REMOVED: Property FinalClass#$decreasedVisibilityPublicProperty was removed',
                    '[BC] REMOVED: Property FinalClass#$removedPublicProperty was removed',
                ],
            ],
            'removed trait use from class' => [
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

trait PropertyTrait {
	/** @api */
    protected $testProperty;
}

/** @api */
class TestClass
{
    use PropertyTrait;
}
PHP
                    ,
                    $locator,
                )))->reflectClass('TestClass'),
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

/** @api */
class TestClass
{
}
PHP
                    ,
                    $locator,
                )))->reflectClass('TestClass'),
                ['[BC] REMOVED: Property TestClass#$testProperty was removed'],
            ],
            'removed property from trait' => [
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

trait PropertyTrait {
	/** @api */
    protected $testProperty;
}

/** @api */
class TestClass
{
    use PropertyTrait;
}
PHP
                    ,
                    $locator,
                )))->reflectClass('TestClass'),
                (new DefaultReflector(new StringSourceLocator(
                    <<<'PHP'
<?php

trait PropertyTrait {
    
}

/** @api */
class TestClass
{
    use PropertyTrait;
}
PHP
                    ,
                    $locator,
                )))->reflectClass('TestClass'),
                ['[BC] REMOVED: Property TestClass#$testProperty was removed'],
            ],
        ];
    }
}
