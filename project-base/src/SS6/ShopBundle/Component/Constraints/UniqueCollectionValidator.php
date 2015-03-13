<?php

namespace SS6\ShopBundle\Component\Constraints;

use SS6\ShopBundle\Component\Constraints\UniqueCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCollectionValidator extends ConstraintValidator {

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof UniqueCollection) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueCollection::class);
		}

		if (!is_array($constraint->fields) || count($constraint->fields) === 0) {
			throw new \Symfony\Component\Validator\Exception\MissingOptionsException(
				'Option "fileds" is either missing or is not an array',
				['fields']
			);
		}

		foreach ($values as $index1 => $value1) {
			foreach ($values as $index2 => $value2) {
				if ($index1 !== $index2) {
					if ($this->areValuesEqualInFields($constraint->fields, $value1, $value2)) {
						$this->context->addViolation($constraint->message);
						return;
					}
				}
			}
		}
	}

	/**
	 * @param array $fields
	 * @param mixed $value1
	 * @param mixed $value2
	 * @return boolean
	 */
	private function areValuesEqualInFields(array $fields, $value1, $value2) {
		foreach ($fields as $field) {
			$fieldValue1 = $this->getFieldValue($value1, $field);
			$fieldValue2 = $this->getFieldValue($value2, $field);

			if ($fieldValue1 !== $fieldValue2) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param mixed $value
	 * @param string $field
	 * @return mixed
	 * @throws \Symfony\Component\Validator\Exception\ConstraintDefinitionException
	 */
	private function getFieldValue($value, $field) {
		$propertyAccessor = PropertyAccess::createPropertyAccessor();
		if (!$propertyAccessor->isReadable($value, $field)) {
			throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException();
		}

		return $propertyAccessor->getValue($value, $field);
	}

}
