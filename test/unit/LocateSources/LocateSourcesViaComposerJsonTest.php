<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility\LocateSources;

use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPUnit\Framework\TestCase;
use Roave\BackwardCompatibility\LocateSources\LocateSourcesViaComposerJson;

/**
 * @covers \Roave\BackwardCompatibility\LocateSources\LocateSourcesViaComposerJson
 */
final class LocateSourcesViaComposerJsonTest extends TestCase
{
    private LocateSourcesViaComposerJson $locateSources;

    protected function setUp(): void
    {
        parent::setUp();

        $this->locateSources = new LocateSourcesViaComposerJson((new BetterReflection())->astLocator());
    }

    public function testCanLocateClassInMappendAutoloadDefinitions(): void
    {
        $reflector = new ClassReflector(
            $this->locateSources
                ->__invoke(__DIR__ . '/../../asset/located-sources/composer-definition-with-everything')
        );

        self::assertSame(
            'baz\\LocatedClass',
            $reflector
                ->reflect('baz\\LocatedClass')
                ->getName()
        );
    }
}
