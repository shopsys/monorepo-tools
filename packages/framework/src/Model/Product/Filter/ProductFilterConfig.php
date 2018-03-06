<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ProductFilterConfig
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    private $parameterChoices;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    private $flagChoices;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    private $brandChoices;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\PriceRange
     */
    private $priceRange;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[] $flagChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[] $brandChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\PriceRange $priceRange
     */
    public function __construct(
        array $parameterChoices,
        array $flagChoices,
        array $brandChoices,
        PriceRange $priceRange
    ) {
        $this->parameterChoices = $parameterChoices;
        $this->flagChoices = $flagChoices;
        $this->brandChoices = $brandChoices;
        $this->priceRange = $priceRange;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[]
     */
    public function getParameterChoices()
    {
        return $this->parameterChoices;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public function getFlagChoices()
    {
        return $this->flagChoices;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    public function getBrandChoices()
    {
        return $this->brandChoices;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\PriceRange
     */
    public function getPriceRange()
    {
        return $this->priceRange;
    }
}
