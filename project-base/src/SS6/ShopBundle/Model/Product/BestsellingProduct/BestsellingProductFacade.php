<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct;

use DateTime;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;

class BestsellingProductFacade {

	const MAX_RESULTS = 10;
	const ORDERS_CREATED_AT_LIMIT = '-1 month';
	const MAX_SHOW_RESULTS = 3;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository
	 */
	private $bestsellingProductRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService
	 */
	private $bestsellingProductService;

	public function __construct(
		BestsellingProductRepository $bestsellingProductRepository,
		ProductDetailFactory $productDetailFactory,
		BestsellingProductService $bestsellingProductService
	) {
		$this->bestsellingProductRepository = $bestsellingProductRepository;
		$this->productDetailFactory = $productDetailFactory;
		$this->bestsellingProductService = $bestsellingProductService;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getAllOfferedProductDetails($domainId, Category $category, PricingGroup $pricingGroup) {
		$bestsellingProducts = $this->bestsellingProductRepository->getOfferedManualBestsellingProducts(
			$domainId,
			$category,
			$pricingGroup
		);

		$manualBestsellingProductsIndexedByPosition = [];
		foreach ($bestsellingProducts as $bestsellingProduct) {
			$manualBestsellingProductsIndexedByPosition[$bestsellingProduct->getPosition()] = $bestsellingProduct->getProduct();
		}

		$automaticBestsellingProducts = $this->bestsellingProductRepository->getOfferedAutomaticBestsellingProducts(
			$domainId,
			$category,
			$pricingGroup,
			new DateTime(self::ORDERS_CREATED_AT_LIMIT),
			self::MAX_RESULTS
		);

		$combinedBestsellingProducts = $this->bestsellingProductService->combineManualAndAutomaticBestsellingProducts(
			$manualBestsellingProductsIndexedByPosition,
			$automaticBestsellingProducts,
			self::MAX_RESULTS
		);

		return $this->productDetailFactory->getDetailsForProducts($combinedBestsellingProducts);
	}

}
