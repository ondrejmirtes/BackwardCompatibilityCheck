<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use PHPStan\BetterReflection\Reflection\ReflectionClass;

/**
 * A class cannot become an interface without introducing an explicit BC break, since
 * all child classes or implementors need to be changed from `extends` to `implements`,
 * and all instantiations start failing
 */
final class ClassBecameInterface implements ClassBased
{
    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if ($fromClass->isInterface() || ! $toClass->isInterface()) {
            // checking whether a class became an interface is done in `InterfaceBecameClass`
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            Str\format('Class %s became an interface', $fromClass->getName()),
        ));
    }
}
