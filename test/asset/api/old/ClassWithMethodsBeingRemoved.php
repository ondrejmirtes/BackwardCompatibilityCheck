<?php

declare(strict_types=1);

namespace RoaveTestAsset;

class ClassWithMethodsBeingRemoved
{
	/** @api */
    public function removedPublicMethod() : void
    {
    }

	/** @api */
    public function nameCaseChangePublicMethod() : void
    {
    }

	/** @api */
    public function keptPublicMethod() : void
    {
    }

	/** @api */
    protected function removedProtectedMethod() : void
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
    private function removedPrivateMethod() : void
    {
    }
    private function nameCaseChangePrivateMethod() : void
    {
    }
    private function keptPrivateMethod() : void
    {
    }

    public function removedInternalPublicMethod() : void
    {
    }

    protected function removedInternalProtectedMethod() : void
    {
    }

    protected function removedInternalPrivateMethod() : void
    {
    }
}
