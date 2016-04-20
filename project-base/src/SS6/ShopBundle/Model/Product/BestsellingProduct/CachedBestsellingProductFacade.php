<?php

namespace SS6\ShopBundle\Model\Product\BestsellingProduct;

use Doctrine\Common\Cache\CacheProvider;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade;
use SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\ProductService;

class CachedBestsellingProductFacade {

	const LIFETIME = 43200; // 12h

	/**
	 * @var \SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductFacade
	 */
	private $bestsellingProductFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Detail\ProductDetailFactory
	 */
	private $productDetailFactory;

	/**
	 * @var \Doctrine\Common\Cache\CacheProvider
	 */
	private $cacheProvider;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductService
	 */
	private $productService;

	public function __construct(
		CacheProvider $cacheProvider,
		BestsellingProductFacade $bestsellingProductFacade,
		ProductDetailFactory $productDetailFactory,
		ProductRepository $productRepository,
		ProductService $productService
	) {
		$this->cacheProvider = $cacheProvider;
		$this->bestsellingProductFacade = $bestsellingProductFacade;
		$this->productDetailFactory = $productDetailFactory;
		$this->productRepository = $productRepository;
		$this->productService = $productService;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	public function getAllOfferedProductDetails($domainId, Category $category, PricingGroup $pricingGroup) {
		$cacheId = $this->getCacheId($domainId, $category, $pricingGroup);
		$orderedProductIds = $this->cacheProvider->fetch($cacheId);

		if ($orderedProductIds === false) {
			$bestsellingProductDetails = $this->bestsellingProductFacade->getAllOfferedProductDetails(
				$domainId,
				$category,
				$pricingGroup
			);
			$this->saveToCache($bestsellingProductDetails, $cacheId);

			return $bestsellingProductDetails;
		} else {
			return $this->getOrderedProductDetails($domainId, $pricingGroup, $orderedProductIds);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Detail\ProductDetail[] $bestsellingProductDetails
	 * @param string $cacheId
	 */
	private function saveToCache(array $bestsellingProductDetails, $cacheId) {
		$orderedProductIds = [];
		foreach ($bestsellingProductDetails as $productDetail) {
			$orderedProductIds[] = $productDetail->getProduct()->getId();
		}

		$this->cacheProvider->save($cacheId, $orderedProductIds, self::LIFETIME);
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param int[] $orderedProductIds
	 * @return \SS6\ShopBundle\Model\Product\Detail\ProductDetail[]
	 */
	private function getOrderedProductDetails($domainId, PricingGroup $pricingGroup, array $orderedProductIds) {
		$products = $this->productRepository->getOfferedByIds($domainId, $pricingGroup, $orderedProductIds);
		$orderedProducts = $this->productService->sortProductsByProductIds($products, $orderedProductIds);

		return $this->productDetailFactory->getDetailsForProducts($orderedProducts);
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return string
	 */
	private function getCacheId($domainId, Category $category, PricingGroup $pricingGroup) {
		return $domainId . '_' . $category->getId() . '_' . $pricingGroup->getId();
	}

}
