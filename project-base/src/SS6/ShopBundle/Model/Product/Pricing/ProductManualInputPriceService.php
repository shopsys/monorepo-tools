<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

class ProductManualInputPriceService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice $productManualInputPrice
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice
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
