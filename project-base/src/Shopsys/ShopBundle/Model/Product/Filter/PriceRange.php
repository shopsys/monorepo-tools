<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

class PriceRange
{
    /**
     * @var string
     */
    private $minimalPrice;

    /**
     * @var string
     */
    private $maximalPrice;

    /**
     * @param string|null $minimalPrice
     * @param string|null $maximalPrice
     */
    public function __construct($minimalPrice, $maximalPrice)
    {
        $this->minimalPrice = $minimalPrice === null ? '0' : $minimalPrice;
        $this->maximalPrice = $maximalPrice === null ? '0' : $maximalPrice;
    }

    /**
     * @return string
     */
    public function getMinimalPrice()
    {
        return $this->minimalPrice;
    }

    /**
     * @return string
     */
    public function getMaximalPrice()
    {
        return $this->maximalPrice;
    }
}
