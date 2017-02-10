<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Pricing\Price;

class ProductPrice extends Price
{
    /**
     * @var bool
     */
    private $priceFrom;

    /**
     * @param \Shopsys\ShopBundle\Model\Pricing\Price $price
     * @param bool $priceFrom
     */
    public function __construct(Price $price, $priceFrom) {
        $this->priceFrom = $priceFrom;
        parent::__construct($price->getPriceWithoutVat(), $price->getPriceWithVat());
    }

    /**
     * @return bool
     */
    public function isPriceFrom() {
        return $this->priceFrom;
    }
}
