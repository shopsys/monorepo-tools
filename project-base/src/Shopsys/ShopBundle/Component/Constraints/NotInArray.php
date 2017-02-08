<?php

namespace SS6\ShopBundle\Component\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotInArray extends Constraint {

	/**
	 * @var string
	 */
	public $message = 'Value must not be neither of following: {{ array }}';

	/**
	 * @var array
	 */
	public $array = [];

	public function getRequiredOptions() {
		return [
			'array',
		];
	}

}
