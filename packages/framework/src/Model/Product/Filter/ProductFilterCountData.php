<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ProductFilterCountData
{
    /**
     * @var int
     */
    public $countInStock;

    /**
     * @var int[]
     */
    public $countByBrandId;

    /**
     * @var int[]
     */
    public $countByFlagId;

    /**
     * @var int[][]
     */
    public $countByParameterIdAndValueId;
}
