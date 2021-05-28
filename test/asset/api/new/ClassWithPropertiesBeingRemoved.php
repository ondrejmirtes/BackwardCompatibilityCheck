<?php

declare(strict_types=1);

namespace RoaveTestAsset;

class ClassWithPropertiesBeingRemoved
{
	/** @api */
    public $NameCaseChangePublicProperty;

	/** @api */
    public $keptPublicProperty;

	/** @api */
    protected $NameCaseChangeProtectedProperty;

	/** @api */
    protected $keptProtectedProperty;

    private $NameCaseChangePrivateProperty;
    private $keptPrivateProperty;
}
