<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use SS6\ShopBundle\Component\Router\CurrentDomainRouter;
use SS6\ShopBundle\Twig\ImageExtension;

class ProductSearchService {

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
	 * @param string $searchText
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
			'totalProductCount' => $totalProductCount,
			'products' => [],
			'searchUrl' => $this->router->generate('front_product_search', ['q' => $searchText]),
		];

		foreach ($products as $product) {
			$responseData['products'][] = [
				'name' => $product->getName(),
				'url' => $this->router->generate('front_product_detail', ['id' => $product->getId()]),
				'imageUrl' => $this->imageExtension->getImageUrl($product, 'thumbnail'),
			];
		}

		return $responseData;
	}

}
