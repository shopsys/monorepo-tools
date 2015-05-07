<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct;

class BestsellingProductService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProductsIndexedByPosition
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProducts
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	public function combineManualAndAutomaticBestsellingProducts(
		array $manualBestsellingProductsIndexedByPosition,
		array $automaticBestsellingProducts,
		$maxResults
	) {
		$automaticBestsellingProductsWithoutDuplicates = $this->getAutomaticBestsellingProductsWithoutDuplicates(
			$manualBestsellingProductsIndexedByPosition, $automaticBestsellingProducts
		);
		$combinedBestsellingProducts = $this->getCombinedBestsellingProducts(
			$manualBestsellingProductsIndexedByPosition, $automaticBestsellingProductsWithoutDuplicates, $maxResults
		);
		return $combinedBestsellingProducts;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProductsIndexedByPosition
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProducts
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getAutomaticBestsellingProductsWithoutDuplicates(
		array $manualBestsellingProductsIndexedByPosition,
		array $automaticBestsellingProducts
	) {
		foreach ($manualBestsellingProductsIndexedByPosition as $manualBestsellingProduct) {
			$automaticBestsellingProductIndex = array_search($manualBestsellingProduct, $automaticBestsellingProducts, true);
			if ($automaticBestsellingProductIndex !== false) {
				unset($automaticBestsellingProducts[$automaticBestsellingProductIndex]);
			}
		}
		return $automaticBestsellingProducts;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $manualBestsellingProductsIndexedByPosition
	 * @param \SS6\ShopBundle\Model\Product\Product[] $automaticBestsellingProductsWithoutDuplicates
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Product\Product[]
	 */
	private function getCombinedBestsellingProducts(
		array $manualBestsellingProductsIndexedByPosition,
		array $automaticBestsellingProductsWithoutDuplicates,
		$maxResults
	) {
		$combinedBestsellingProducts = [];
		for ($position = 0; $position < $maxResults; $position++) {
			if (array_key_exists($position, $manualBestsellingProductsIndexedByPosition)) {
				$combinedBestsellingProducts[] = $manualBestsellingProductsIndexedByPosition[$position];
			} elseif (count($automaticBestsellingProductsWithoutDuplicates) > 0) {
				$combinedBestsellingProducts[] = array_shift($automaticBestsellingProductsWithoutDuplicates);
			}
		}
		return $combinedBestsellingProducts;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $bestsellingProducts
	 * @return boolean
	 */
	public function areProductsDuplicate(array $bestsellingProducts) {
		$areProductsDuplicate = false;
		foreach ($bestsellingProducts as $key1 => $product1) {
			if ($product1 === null) {
				continue;
			}
			$numberOfBestsellingProducts = count($bestsellingProducts);
			for ($key2 = $key1 + 1; $key2 < $numberOfBestsellingProducts; $key2++) {
				$product2 = $bestsellingProducts[$key2];
				if ($product2 === null) {
					continue;
				}
				if ($product1 === $product2) {
					$areProductsDuplicate = true;
					break 2;
				}
			}
		}
		return $areProductsDuplicate;
	}

}
