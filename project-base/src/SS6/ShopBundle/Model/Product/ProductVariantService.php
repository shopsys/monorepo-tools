<?php

namespace SS6\ShopBundle\Model\Product;

use SS6\ShopBundle\Model\Product\Product;

class ProductVariantService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 */
	public function checkProductVariantType(Product $mainProduct, $variants) {
		if (in_array($mainProduct, $variants, true)) {
			throw new \SS6\ShopBundle\Model\Product\Exception\MainVariantCannotBeVariantException($mainProduct->getId());
		}
		if ($mainProduct->isMainVariant()) {
			throw new \SS6\ShopBundle\Model\Product\Exception\MainVariantCannotBeMainVariantException($mainProduct->getId());
		}
	}

}
