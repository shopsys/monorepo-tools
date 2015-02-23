<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Twig\ImageExtension;

class ProductSearchService {

	const RESULT_LABEL = 'label';
	const RESULT_PRODUCT_IMAGE_URL = 'imageUrl';
	const RESULT_PRODUCT_NAME = 'name';
	const RESULT_PRODUCT_URL = 'url';
	const RESULT_PRODUCTS = 'products';
	const RESULT_SEARCH_URL = 'searchUrl';
	const RESULT_TOTAL_PRODUCT_COUNT = 'totalProductCount';

	/**
	 * @var \SS6\ShopBundle\Twig\ImageExtension
	 */
	private $imageExtension;

	/**
	 * @var \SS6\ShopBundle\Component\Router\CurrentDomainRouter
	 */
	private $router;

	public function __construct(
		ImageExtension $imageExtension,
		CurrentDomainRouter $router
	) {
		$this->imageExtension = $imageExtension;
		$this->router = $router;
	}

	/**
	 * @param string|null $searchText
	 * @param \SS6\ShopBundle\Model\Product\Detail\ProductDetail[] $products
	 * @param int $totalProductCount
	 * @return array
	 */
	public function getSearchAutocompleteData(
		$searchText,
		array $products,
		$totalProductCount
	) {
		$responseData = [
			self::RESULT_TOTAL_PRODUCT_COUNT => $totalProductCount,
			self::RESULT_PRODUCTS => [],
			self::RESULT_SEARCH_URL => $this->router->generate('front_product_search', ['q' => $searchText]),
		];

		foreach ($products as $product) {
			$responseData[self::RESULT_PRODUCTS][] = [
				self::RESULT_PRODUCT_NAME => $product->getName(),
				self::RESULT_PRODUCT_URL => $this->router->generate('front_product_detail', ['id' => $product->getId()]),
				self::RESULT_PRODUCT_IMAGE_URL => $this->imageExtension->getImageUrl($product, 'thumbnail'),
			];
		}

		return $responseData;
	}

}
