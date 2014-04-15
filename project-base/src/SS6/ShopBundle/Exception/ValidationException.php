<?php

namespace SS6\ShopBundle\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationException extends Exception {
	/**
	 * @var ConstraintViolationList
	 */
	private $constraintViolations;

	/**
	 * @param \Symfony\Component\Validator\ConstraintViolationList $constraintViolations
	 */
	public function __construct(ConstraintViolationList $constraintViolations) {
		$this->constraintViolations = $constraintViolations;
		
		$messages = array();
		foreach ($constraintViolations as $constraintViolation) {
			/* @var $constraintViolation ConstraintViolation */
			$messages[] = $constraintViolation->getMessage();
		}
		
		parent::__construct(implode("\n", $messages), 0, null);
	}
	
	/**
	 * @return ConstraintViolationList
	 */
	public function getConstraintViolations() {
		return $this->constraintViolations;
	}
}
