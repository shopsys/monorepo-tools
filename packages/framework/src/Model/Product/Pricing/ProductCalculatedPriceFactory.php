<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductCalculatedPriceFactory implements ProductCalculatedPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string|null $priceWithVat
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductCalculatedPrice
     */
    public function create(
        Product $product,
        PricingGroup $pricingGroup,
        ?string $priceWithVat
    ): ProductCalculatedPrice {
        return new ProductCalculatedPrice($product, $pricingGroup, $priceWithVat);
    }
}
