<?php

namespace SS6\ShopBundle\Component\Constrains;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotSelectedDomainToShow extends Constraint {

	public $message = 'You have to select any domain.';
}