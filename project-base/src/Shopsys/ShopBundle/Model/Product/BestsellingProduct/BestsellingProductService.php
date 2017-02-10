<?php

namespace Shopsys\ShopBundle\Model\Product\BestsellingProduct;

class BestsellingProductService {

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $manualProductsIndexedByPosition
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $automaticProducts
	 * @param int $maxResults
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]
	 */
	public function combineManualAndAutomaticProducts(
		array $manualProductsIndexedByPosition,
		array $automaticProducts,
		$maxResults
	) {
		$automaticProductsExcludingManual = $this->getAutomaticProductsExcludingManual(
			$automaticProducts,
			$manualProductsIndexedByPosition
		);
		$combinedProducts = $this->getCombinedProducts(
			$manualProductsIndexedByPosition,
			$automaticProductsExcludingManual,
			$maxResults
		);
		return $combinedProducts;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $automaticProducts
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $manualProducts
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]
	 */
	private function getAutomaticProductsExcludingManual(
		array $automaticProducts,
		array $manualProducts
	) {
		foreach ($manualProducts as $manualProduct) {
			$automaticProductKey = array_search($manualProduct, $automaticProducts, true);
			if ($automaticProductKey !== false) {
				unset($automaticProducts[$automaticProductKey]);
			}
		}

		return $automaticProducts;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $manualProductsIndexedByPosition
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $automaticProductsExcludingManual
	 * @param int $maxResults
	 * @return \Shopsys\ShopBundle\Model\Product\Product[]
	 */
	private function getCombinedProducts(
		array $manualProductsIndexedByPosition,
		array $automaticProductsExcludingManual,
		$maxResults
	) {
		$combinedProducts = [];
		for ($position = 0; $position < $maxResults; $position++) {
			if (array_key_exists($position, $manualProductsIndexedByPosition)) {
				$combinedProducts[] = $manualProductsIndexedByPosition[$position];
			} elseif (count($automaticProductsExcludingManual) > 0) {
				$combinedProducts[] = array_shift($automaticProductsExcludingManual);
			}
		}
		return $combinedProducts;
	}

}
