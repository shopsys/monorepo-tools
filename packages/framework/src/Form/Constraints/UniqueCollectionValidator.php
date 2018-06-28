<?php

namespace Shopsys\FrameworkBundle\Form\Constraints;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCollectionValidator extends ConstraintValidator
{
    /**
     * @param array $values
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($values, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueCollection) {
            throw new \Symfony\Component\Validator\Exception\UnexpectedTypeException($constraint, UniqueCollection::class);
        }

        if ($constraint->fields !== null && !is_array($constraint->fields)) {
            throw new \Symfony\Component\Validator\Exception\InvalidOptionsException(
                'Option "fields" must be array or null',
                ['fields']
            );
        }

        if (!is_bool($constraint->allowEmpty)) {
            throw new \Symfony\Component\Validator\Exception\InvalidOptionsException(
                'Option "allowEmpty" must be boolean',
                ['allowEmpty']
            );
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
     * @param \Shopsys\FrameworkBundle\Form\Constraints\UniqueCollection $constraint
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     */
    private function areValuesEqual(UniqueCollection $constraint, $value1, $value2)
    {
        if ($constraint->allowEmpty) {
            if ($value1 === null || $value2 === null) {
                return false;
            }
        }

        if ($constraint->fields === null) {
            return $value1 === $value2;
        } else {
            return $this->areValuesEqualInFields($constraint->fields, $value1, $value2);
        }
    }

    /**
     * @param array $fields
     * @param mixed $value1
     * @param mixed $value2
     * @return bool
     */
    private function areValuesEqualInFields(array $fields, $value1, $value2)
    {
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
     */
    private function getFieldValue($value, $field)
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return $propertyAccessor->getValue($value, $field);
    }
}
