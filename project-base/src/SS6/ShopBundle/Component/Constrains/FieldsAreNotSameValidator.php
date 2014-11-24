<?php

namespace SS6\ShopBundle\Component\Constrains;

use Symfony\Component\PropertyAccess\PropertyAccess;

use \Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FieldsAreNotSameValidator extends ConstraintValidator {

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof FieldsAreNotSame) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, FieldsAreNotSame::class);
		}

		$propertyAccessor = PropertyAccess::createPropertyAccessor();
		if ($propertyAccessor->getValue($values, $constraint->field1) === $propertyAccessor->getValue($values, $constraint->field2)) {
			$this->context->addViolationAt($constraint->fieldToShowError, $constraint->message);
			return;
		}

	}
}
