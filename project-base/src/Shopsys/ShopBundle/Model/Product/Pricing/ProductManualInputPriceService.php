<?php

namespace Shopsys\ShopBundle\Model\Product\Pricing;

use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductManualInputPriceService
{

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $inputPrice
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice $productManualInputPrice
     * @return \Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice
     */
    public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice, $productManualInputPrice) {
        if ($productManualInputPrice === null) {
            $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, $inputPrice);
        } else {
            $productManualInputPrice->setInputPrice($inputPrice);
        }
        return $productManualInputPrice;
    }

}
