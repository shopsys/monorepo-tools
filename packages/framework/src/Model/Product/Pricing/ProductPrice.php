<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Price;

class ProductPrice extends Price
{
    /**
     * @var bool
     */
    private $priceFrom;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @param bool $priceFrom
     */
    public function __construct(Price $price, $priceFrom)
    {
        $this->priceFrom = $priceFrom;
        parent::__construct($price->getPriceWithoutVat(), $price->getPriceWithVat());
    }

    /**
     * @return bool
     */
    public function isPriceFrom()
    {
        return $this->priceFrom;
    }
}
