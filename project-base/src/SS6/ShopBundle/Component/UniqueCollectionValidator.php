<?php

namespace SS6\ShopBundle\Component;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCollectionValidator extends ConstraintValidator {

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof UniqueCollection) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueCollection::class);
		}

		foreach ($values as $index1 => $value1) {
			foreach ($values as $index2 => $value2) {
				if ($index1 !== $index2) {
					if ($this->areValuesEqual($constraint, $value1, $value2)) {
						$this->context->addViolation($constraint->message);
						return;
					}
				}
			}
		}
	}

	/**
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return boolean
	 */
	private function areValuesEqual(Constraint $constraint, $value1, $value2) {
		if (is_array($constraint->fields) && !empty($constraint->fields)) {
			return $this->areValuesEqualInFields($constraint->fields, $value1, $value2);
		}

		return $value1 === $value2;
	}

	/**
	 * @param array $fields
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return boolean
	 * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
	 */
	private function areValuesEqualInFields(array $fields, $value1, $value2) {
		foreach ($fields as $field) {
			$methodName = 'get' . ucfirst($field);


			if (!is_callable(array($value1, $methodName)) || !is_callable(array($value2, $methodName))) {
				throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException();
			}

			if ($value1->$methodName() !== $value2->$methodName()) {
				return false;
			}
		}

		return true;
	}

}
