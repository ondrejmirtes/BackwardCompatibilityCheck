<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ClassBased;
use Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased\ExcludeAnonymousClasses;
use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflection\ReflectionClass;
use PHPStan\BetterReflection\Reflector\DefaultReflector;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

use function reset;

final class ExcludeAnonymousClassesTest extends TestCase
{
    public function testNormalClassesAreNotExcluded(): void
    {
        $locator        = (new BetterReflection())->astLocator();
        $reflector      = new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

class ANormalClass {}
PHP
            ,
            $locator,
        ));
        $fromReflection = $reflector->reflectClass('ANormalClass');
        $toReflection   = $reflector->reflectClass('ANormalClass');

        $check = $this->createMock(ClassBased::class);
        $check->expects(self::once())
            ->method('__invoke')
            ->with($fromReflection, $toReflection)
            ->willReturn(Changes::empty());

        (new ExcludeAnonymousClasses($check))($fromReflection, $toReflection);
    }

    public function testAnonymousClassesAreExcluded(): void
    {
        $locator                  = (new BetterReflection())->astLocator();
        $reflector                = new DefaultReflector(new StringSourceLocator(
            <<<'PHP'
<?php

$anonClass = new class {};
PHP
            ,
            $locator,
        ));
        $allClasses               = $reflector->reflectAllClasses();
        $anonymousClassReflection = reset($allClasses);

        self::assertInstanceOf(ReflectionClass::class, $anonymousClassReflection);

        $check = $this->createMock(ClassBased::class);
        $check->expects(self::never())->method('__invoke');

        (new ExcludeAnonymousClasses($check))($anonymousClassReflection, $anonymousClassReflection);
    }
}
