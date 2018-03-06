<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductManualInputPriceService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $inputPrice
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice $productManualInputPrice
     * @return \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice
     */
    public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice, $productManualInputPrice)
    {
        if ($productManualInputPrice === null) {
            $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, $inputPrice);
        } else {
            $productManualInputPrice->setInputPrice($inputPrice);
        }
        return $productManualInputPrice;
    }
}
