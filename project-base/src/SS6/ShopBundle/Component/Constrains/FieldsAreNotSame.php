<?php

namespace SS6\ShopBundle\Component\Constrains;

use \Symfony\Component\Validator\Constraint;

class FieldsAreNotSame extends Constraint {

	public $field1;

	public $field2;

	public $fieldToShowError;

	public $message = 'Fields must not be same';
}
