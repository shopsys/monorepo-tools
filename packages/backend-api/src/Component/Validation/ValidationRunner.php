<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Component\Validation;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

/**
 * @experimental
 */
class ValidationRunner
{
    /**
     * @param array $apiData
     * @param \Symfony\Component\Validator\Constraints\Collection $constraintCollection
     * @return string[]
     */
    public function runValidation(array $apiData, Collection $constraintCollection): array
    {
        $validator = Validation::createValidator();

        /** @var \Symfony\Component\Validator\ConstraintViolationList $violations */
        $violations = $validator->validate(
            $apiData,
            $constraintCollection
        );

        $errors = [];

        if ($violations->count()) {
            $errors = [];
            foreach ($violations->getIterator() as $violation) {
                $propertyPath = $this->convertBracketToDotNotation($violation->getPropertyPath());
                $errors[$propertyPath] = $violation->getMessage();
            }
        }

        return $errors;
    }

    /**
     * @param string $propertyPath
     * @return string
     */
    protected function convertBracketToDotNotation(string $propertyPath): string
    {
        $search = ['][', ']', '['];
        $replace = ['.', '', ''];
        return str_replace($search, $replace, $propertyPath);
    }
}
