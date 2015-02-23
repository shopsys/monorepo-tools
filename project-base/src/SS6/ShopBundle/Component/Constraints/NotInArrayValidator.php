<?php

namespace SS6\ShopBundle\Component\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotInArrayValidator extends ConstraintValidator {

	/**
	 * @param string $value
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 */
	public function validate($value, Constraint $constraint) {
		if (!$constraint instanceof NotInArray) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, NotInArray::class);
		}

		if (in_array($value, $constraint->array)) {
			$this->context->addViolation(
				$constraint->message, [
					'{{ array }}' => $this->formatArray($value),
				]
			);
		}

	}

	/**
	 * @param mixed $values
	 * @return string
	 */
	private function formatArray($values) {
		if (!is_array($values)) {
			return $values;
		}
		$output = '';
		$count = 1;
		foreach ($values as $value) {
			if ($count === count($values)) {
				$output .= $value;
			} else {
				$output .= $value . ', ';
			}
			$count++;
		}

		return $output;
	}
}
