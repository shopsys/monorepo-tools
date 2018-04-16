<?php

namespace Shopsys\ProductFeed\GoogleBundle\Model\Product;

class GoogleProductDomainData
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
     * @var int
     */
    public $domainId;

    public function __construct()
    {
        $this->domainId = 0;
        $this->show = false;
    }
}
