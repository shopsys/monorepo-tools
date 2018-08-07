<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductManualInputPriceFactory implements ProductManualInputPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string|null $inputPrice
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice
     */
    public function create(
        Product $product,
        PricingGroup $pricingGroup,
        ?string $inputPrice
    ): ProductManualInputPrice {
        return new ProductManualInputPrice($product, $pricingGroup, $inputPrice);
    }
}
