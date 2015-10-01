<?php

namespace SS6\ShopBundle\Model\Product\Collection;

class ProductCollectionService {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product $products
	 * @param \SS6\ShopBundle\Component\Image\Image $imagesByProductId
	 * @return array
	 */
	public function getImagesIndexedByProductId(array $products, array $imagesByProductId) {
		$imagesOrNullByProductId = [];

		foreach ($products as $product) {
			if (array_key_exists($product->getId(), $imagesByProductId)) {
				$imagesOrNullByProductId[$product->getId()] = $imagesByProductId[$product->getId()];
			} else {
				$imagesOrNullByProductId[$product->getId()] = null;
			}
		}

		return $imagesOrNullByProductId;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Parameter\ProductParameterValue[] $productParameterValues
	 * @param string $locale
	 * @return string[productId][paramName]
	 */
	public function getParametersIndexedByProductId(
		array $productParameterValues,
		$locale
	) {
		$productParameterValuesIndexedByProductIdAndParameterName = [];
		foreach ($productParameterValues as $productParameterValue) {
			$parameterName = $productParameterValue->getParameter()->getName($locale);
			$productId = $productParameterValue->getProduct()->getId();
			$productParameterValuesIndexedByProductIdAndParameterName[$productId][$parameterName] =
				$productParameterValue->getValue()->getText();
		}

		return $productParameterValuesIndexedByProductIdAndParameterName;
	}
}
