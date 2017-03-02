<?php

namespace Shopsys\ShopBundle\Component\Transformers;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\DataTransformerInterface;

class InverseMultipleChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var \Symfony\Component\Form\ChoiceList\ChoiceListInterface
     */
    private $choiceList;

    /**
     * @param \Symfony\Component\Form\ChoiceList\ChoiceListInterface $choiceList
     */
    public function __construct(ChoiceListInterface $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        return $this->getInvertedValues($value, $this->choiceList->getChoices());
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (!is_array($value)) {
            return null;
        }

        return $this->getInvertedValues($value, $this->choiceList->getChoices());
    }

    /**
     * @param array $inputValues
     * @param array $allChoices
     */
    private function getInvertedValues(array $inputValues, array $allChoices)
    {
        $outputValues = [];

        foreach ($allChoices as $choice) {
            if (!in_array($choice, $inputValues, true)) {
                $outputValues[] = $choice;
            }
        }

        return $outputValues;
    }
}
