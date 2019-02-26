<?php

namespace Shopsys\ProductFeed\ZboziBundle\Model\Product;

class ZboziProductDomainData
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product|null
     */
    public $product;

    /**
     * @var bool
     */
    public $show;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $cpc;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Money\Money|null
     */
    public $cpcSearch;

    /**
     * @var int
     */
    public $domainId;

    public function __construct()
    {
        $this->domainId = 0;
        $this->show = false;
    }
}
