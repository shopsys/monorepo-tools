<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

interface ProductCalculatedPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $priceWithVat
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice
     */
    public function create(
        Product $product,
        PricingGroup $pricingGroup,
        ?Money $priceWithVat
    ): ProductCalculatedPrice;
}
