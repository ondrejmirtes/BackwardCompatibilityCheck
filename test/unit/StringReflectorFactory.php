<?php

declare(strict_types=1);

namespace RoaveTest\BackwardCompatibility;

use PHPStan\BetterReflection\BetterReflection;
use PHPStan\BetterReflection\Reflector\ClassReflector;
use PHPStan\BetterReflection\SourceLocator\Exception\EmptyPhpSourceCode;
use PHPStan\BetterReflection\SourceLocator\SourceStubber\ReflectionSourceStubber;
use PHPStan\BetterReflection\SourceLocator\Type\AggregateSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\EvaledCodeSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\PhpInternalSourceLocator;
use PHPStan\BetterReflection\SourceLocator\Type\StringSourceLocator;

final class StringReflectorFactory
{
    /**
     * @throws EmptyPhpSourceCode
     */
    public function __invoke(string $sourceCode): ClassReflector
    {
        $astLocator = (new BetterReflection())->astLocator();
        $stubber    = new ReflectionSourceStubber();

        return new ClassReflector(
            new AggregateSourceLocator([
                new PhpInternalSourceLocator($astLocator, $stubber),
                new EvaledCodeSourceLocator($astLocator, $stubber),
                new StringSourceLocator($sourceCode, $astLocator),
            ])
        );
    }
}
