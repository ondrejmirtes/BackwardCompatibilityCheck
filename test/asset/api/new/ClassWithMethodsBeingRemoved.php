<?php

declare(strict_types=1);

namespace RoaveTestAsset;

class ClassWithMethodsBeingRemoved
{
	/** @api */
    public function nameCaseChangePublicMethod() : void
    {
    }

	/** @api */
    public function keptPublicMethod() : void
    {
    }

	/** @api */
    protected function nameCaseChangeProtectedMethod() : void
    {
    }

	/** @api */
    protected function keptProtectedMethod() : void
    {
    }
    private function nameCaseChangePrivateMethod() : void
    {
    }
    private function keptPrivateMethod() : void
    {
    }
}
