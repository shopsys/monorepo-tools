<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ProductFilterData
{
    /**
     * @var string
     */
    public $minimalPrice;

    /**
     * @var string
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
}
