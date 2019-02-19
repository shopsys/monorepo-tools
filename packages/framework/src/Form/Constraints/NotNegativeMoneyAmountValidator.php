<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotNegativeMoneyAmountValidator extends ConstraintValidator
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotNegativeMoneyAmount) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, NotNegativeMoneyAmount::class);
        }

        if ($value === null) {
            return;
        }

        if (!($value instanceof Money)) {
            throw new \Shopsys\FrameworkBundle\Form\Exception\NotMoneyTypeException($value);
        }

        $zeroMoney = Money::fromInteger(0);
        if ($value->isLessThan($zeroMoney)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
