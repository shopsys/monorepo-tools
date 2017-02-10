<?php

namespace Shopsys\ShopBundle\Model\Product\BestsellingProduct;

use DateTime;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository;
use Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService;
use Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory;

class BestsellingProductFacade {

	const MAX_RESULTS = 10;
	const ORDERS_CREATED_AT_LIMIT = '-1 month';
	const MAX_SHOW_RESULTS = 3;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductRepository
	 */
	private $bestsellingProductRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService
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
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \Shopsys\ShopBundle\Model\Product\Detail\ProductDetail[]
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
