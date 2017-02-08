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

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $currentVariants
	 */
	public function refreshProductVariants(Product $mainProduct, array $currentVariants) {
		$this->unsetRemovedVariants($mainProduct, $currentVariants);
		$this->addNewVariants($mainProduct, $currentVariants);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $currentVariants
	 */
	private function unsetRemovedVariants(Product $mainProduct, array $currentVariants) {
		foreach ($mainProduct->getVariants() as $originalVariant) {
			if (!in_array($originalVariant, $currentVariants, true)) {
				$originalVariant->unsetMainVariant();
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $currentVariants
	 */
	private function addNewVariants(Product $mainProduct, array $currentVariants) {
		foreach ($currentVariants as $currentVariant) {
			if (!in_array($currentVariant, $mainProduct->getVariants(), true)) {
				$mainProduct->addVariant($currentVariant);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\ProductEditData $mainVariantEditData
	 * @param \SS6\ShopBundle\Model\Product\Product $mainProduct
	 * @param \SS6\ShopBundle\Model\Product\Product[] $variants
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function createMainVariant(ProductEditData $mainVariantEditData, Product $mainProduct, array $variants) {
		$variants[] = $mainProduct;

		return Product::createMainVariant($mainVariantEditData->productData, $variants);
	}

}
