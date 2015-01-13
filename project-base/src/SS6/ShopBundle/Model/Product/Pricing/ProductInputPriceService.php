<?php

namespace SS6\ShopBundle\Model\Product\Pricing;

use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Product;

class ProductInputPriceService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $inputPrice
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice $productInputPrice
	 * @return \SS6\ShopBundle\Model\Product\Pricing\ProductInputPrice
	 */
	public function refresh(Product $product, PricingGroup $pricingGroup, $inputPrice, $productInputPrice) {
		if ($productInputPrice === null) {
			$productInputPrice = new ProductInputPrice($product, $pricingGroup, $inputPrice);
		} else {
			$productInputPrice->setInputPrice($inputPrice);
		}
		return $productInputPrice;
	}

}
