<?php

namespace Shopsys\ShopBundle\Component\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotIdenticalToEmailLocalPartValidator extends ConstraintValidator
{
    /**
     * @param array $values
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($values, Constraint $constraint)
    {
        if (!$constraint instanceof NotIdenticalToEmailLocalPart) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, NotIdenticalToEmailLocalPart::class);
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $password = $propertyAccessor->getValue($values, $constraint->password);
        $email = $propertyAccessor->getValue($values, $constraint->email);

        if (strpos($email, $password . '@') === 0) {
            $this->context->addViolationAt($constraint->errorPath, $constraint->message);
            return;
        }

    }
}
