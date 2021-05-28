<?php

declare(strict_types=1);

namespace RoaveTestAsset;

class ClassWithPropertiesBeingRemoved
{
	/** @api */
    public $removedPublicProperty;

	/** @api */
    public $nameCaseChangePublicProperty;

	/** @api */
    public $keptPublicProperty;

	/** @api */
    protected $removedProtectedProperty;

	/** @api */
    protected $nameCaseChangeProtectedProperty;

	/** @api */
    protected $keptProtectedProperty;
    private $removedPrivateProperty;
    private $nameCaseChangePrivateProperty;
    private $keptPrivateProperty;

    public $removedInternalPublicProperty;

    protected $removedInternalProtectedProperty;

    private $removedInternalPrivateProperty;
}
