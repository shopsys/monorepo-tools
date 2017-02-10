<?php

namespace Shopsys\ShopBundle\Component\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FieldsAreNotIdenticalValidator extends ConstraintValidator
{

    /**
     * @param array $values
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($values, Constraint $constraint) {
        if (!$constraint instanceof FieldsAreNotIdentical) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, FieldsAreNotIdentical::class);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $value1 = $propertyAccessor->getValue($values, $constraint->field1);
        $value2 = $propertyAccessor->getValue($values, $constraint->field2);

        if ($value1 === $value2) {
            $this->context->addViolationAt($constraint->errorPath, $constraint->message);
            return;
        }

    }
}
