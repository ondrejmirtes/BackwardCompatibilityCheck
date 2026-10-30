<?php

declare(strict_types=1);

namespace Roave\BackwardCompatibility\DetectChanges\BCBreak\ClassBased;

use Psl\Str;
use Roave\BackwardCompatibility\Change;
use Roave\BackwardCompatibility\Changes;
use Roave\BackwardCompatibility\InternalHelper;
use Roave\BetterReflection\Reflection\ReflectionClass;

final class ClassBecameFinal implements ClassBased
{
    public function __invoke(ReflectionClass $fromClass, ReflectionClass $toClass): Changes
    {
        if (! InternalHelper::isClassOpenForExtension($fromClass)) {
            return Changes::empty();
        }

        if (! $toClass->isFinal()) {
            return Changes::empty();
        }

        return Changes::fromList(Change::changed(
            Str\format('Class %s became final', $fromClass->getName()),
        ));
    }
}
