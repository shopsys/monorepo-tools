<?php

namespace SS6\ShopBundle\Component\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueProductParameters extends Constraint {

	public $message = 'Product parameters are duplicate.';

}
