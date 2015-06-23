<?php

namespace SS6\ShopBundle\Model\Product\Collection;

class ProductCollectionService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $products
	 * @param \SS6\ShopBundle\Model\Image\Image $imagesByProductId
	 * @return array[]
	 */
	public function getImagesIndexedByProductId(array $products, array $imagesByProductId) {
		$imagesUrlByProductId = [];

		foreach ($products as $product) {
			if (array_key_exists($product->getId(), $imagesByProductId)) {
				$imagesUrlByProductId[$product->getId()] = $imagesByProductId[$product->getId()];
			} else {
				$imagesUrlByProductId[$product->getId()] = null;
			}
		}

		return $imagesUrlByProductId;
	}
}
