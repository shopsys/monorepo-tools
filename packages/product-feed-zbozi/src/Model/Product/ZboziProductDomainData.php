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
     * @var string|null
     */
    public $cpc;

    /**
     * @var string|null
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
