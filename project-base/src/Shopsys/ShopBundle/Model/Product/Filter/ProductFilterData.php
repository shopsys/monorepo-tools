<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

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
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterData[]
     */
    public $parameters = [];

    /**
     * @var bool
     */
    public $inStock;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Flag\Flag[]
     */
    public $flags = [];

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Brand\Brand[]
     */
    public $brands = [];
}
