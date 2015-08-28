<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Product;

class ProductVariantService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 */
	public function checkProductIsNotMainVariant(Product $product) {
		if ($product->isMainVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException($product->getId());
		}
	}

}
