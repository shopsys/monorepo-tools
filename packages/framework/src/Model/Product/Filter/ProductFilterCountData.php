<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

class ProductFilterCountData
{
    /**
     * @var int|null
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

    public function __construct()
    {
        $this->countByBrandId = [];
        $this->countByFlagId = [];
        $this->countByParameterIdAndValueId = [];
    }
}
