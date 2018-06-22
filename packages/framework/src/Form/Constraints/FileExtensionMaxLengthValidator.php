<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileExtensionMaxLengthValidator extends ConstraintValidator
{
    /**
     * @param string $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof FileExtensionMaxLength) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, FileExtensionMaxLength::class);
        }

        if (!$value instanceof File) {
            throw new \Symfony\Component\Validator\Exception\InvalidArgumentException('Value must be instance of ' . File::class);
        }

        if (!is_int($constraint->limit) || $constraint->limit < 0) {
            throw new \Symfony\Component\Validator\Exception\ConstraintDefinitionException('Limit must be integer and greater than zero.');
        }

        if (mb_strlen($value->getExtension()) > $constraint->limit) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '{{ value }}' => $this->formatValue($value->getExtension()),
                    '{{ limit }}' => $this->formatValue($constraint->limit),
                ]
            );
        }
    }
}
