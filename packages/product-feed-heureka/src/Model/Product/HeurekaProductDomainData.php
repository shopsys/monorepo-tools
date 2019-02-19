<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

class HeurekaProductDomainData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public $product;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
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
