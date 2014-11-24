<?php

namespace SS6\ShopBundle\Component\Constrains;

use Symfony\Component\PropertyAccess\PropertyAccess;

use \Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FieldsAreNotIdenticalValidator extends ConstraintValidator {

	/**
	 * @param array $values
	 * @param \Symfony\Component\Validator\Constraint $constraint
	 * @throws \Symfony\Component\Validator\Exception\UnexpectedTypeException
	 */
	public function validate($values, Constraint $constraint) {
		if (!$constraint instanceof FieldsAreNotIdentical) {
			throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, FieldsAreNotIdentical::class);
		}

		$propertyAccessor = PropertyAccess::createPropertyAccessor();
		if ($propertyAccessor->getValue($values, $constraint->field1) === $propertyAccessor->getValue($values, $constraint->field2)) {
			$this->context->addViolationAt($constraint->errorPath, $constraint->message);
			return;
		}

	}
}
