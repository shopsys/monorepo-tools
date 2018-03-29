<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

class HeurekaProductDomainData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public $product;

    /**
     * @var string|null
     */
    public $cpc;

    /**
     * @var int
     */
    public $domainId;

    public function __construct()
    {
        $this->domainId = 0;
    }
}
