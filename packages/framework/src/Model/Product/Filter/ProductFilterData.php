<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ProductFilterData
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $minimalPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $maximalPrice;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData[]
     */
    public $parameters = [];

    /**
     * @var bool
     */
    public $inStock;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[]
     */
    public $flags = [];

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[]
     */
    public $brands = [];

    public function __construct()
    {
        $this->inStock = false;
    }
}
