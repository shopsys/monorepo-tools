<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Contains) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, Contains::class);
        }

        if (mb_strpos($value, $constraint->needle) === false) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ value }}' => $this->formatValue($value),
                    '{{ needle }}' => $this->formatValue($constraint->needle),
                ]
            );
        }
    }
}
