<?php

namespace Shopsys\ShopBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class IndexedChoiceList extends ChoiceList
{

    /**
     * @var array
     */
    private $originalChoices;

    /**
     * @var array
     */
    private $customIndices;

    /**
     * @var array
     */
    private $customValues;

    /**
     * @param array|\Traversable $choices
     * @param array $labels
     * @param array $indices
     * @param array $values
     * @param array $preferredChoices
     */
    public function __construct(
        $choices,
        array $labels,
        array $indices,
        array $values,
        array $preferredChoices = []
    ) {
        $this->originalChoices = $choices;
        $this->customIndices = $indices;
        $this->customValues = $values;

        parent::__construct($choices, $labels, $preferredChoices);
    }

    /**
     * {@inheritdoc}
     */
    protected function createIndex($choice) {
        $key = array_search($choice, $this->originalChoices, true);

        return $this->customIndices[$key];
    }

    /**
     * {@inheritdoc}
     */
    protected function createValue($choice) {
        $key = array_search($choice, $this->originalChoices, true);

        return $this->customValues[$key];
    }

}
