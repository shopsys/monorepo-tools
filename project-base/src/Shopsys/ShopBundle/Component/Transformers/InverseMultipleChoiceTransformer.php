<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\DataTransformerInterface;

class InverseMultipleChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    private $allChoices;

    /**
     * @param array $allChoices Choices from ChoiceType options
     */
    public function __construct(array $allChoices)
    {
        $this->allChoices = $allChoices;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        return $this->getInvertedValues($value);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        return $this->getInvertedValues($value);
    }

    /**
     * @param array $inputValues
     * @return array
     */
    private function getInvertedValues(array $inputValues)
    {
        $outputValues = [];

        foreach ($this->allChoices as $choice) {
            if (!in_array($choice, $inputValues, true)) {
                $outputValues[] = $choice;
            }
        }

        return $outputValues;
    }
}
