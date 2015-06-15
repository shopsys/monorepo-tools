<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterRepository;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductFilterCountRepository {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Product\Filter\ProductFilterRepository
	 */
	private $productFilterRepository;

	public function __construct(
		EntityManager $em,
		ProductRepository $productRepository,
		ProductFilterRepository $productFilterRepository
	) {
		$this->em = $em;
		$this->productRepository = $productRepository;
		$this->productFilterRepository = $productFilterRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataInCategory(
		Category $category,
		$domainId,
		$locale,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productsQueryBuilder = $this->productRepository->getFilteredListableInCategoryQueryBuilder(
			$category,
			$domainId,
			$locale,
			$productFilterData,
			$pricingGroup
		);

		return $this->getProductFilterCountData($productsQueryBuilder);
	}

	/**
	 * @param string|null $searchText
	 * @param int $domainId
	 * @param string $locale
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataForSearch(
		$searchText,
		$domainId,
		$locale,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productsQueryBuilder = $this->productRepository->getFilteredListableForSearchQueryBuilder(
			$searchText,
			$domainId,
			$locale,
			$productFilterData,
			$pricingGroup
		);

		return $this->getProductFilterCountData($productsQueryBuilder);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	private function getProductFilterCountData(
		QueryBuilder $productsQueryBuilder
	) {
		$productFilterCountData = new ProductFilterCountData();
		$productFilterCountData->countInStock = $this->getCountInStock($productsQueryBuilder);

		return $productFilterCountData;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 */
	private function getCountInStock(QueryBuilder $productsQueryBuilder) {
		$productsInStockQueryBuilder = clone $productsQueryBuilder;

		$this->productFilterRepository->filterByStock($productsInStockQueryBuilder, true);
		$productsInStockQueryBuilder
			->select('COUNT(p)')
			->resetDQLPart('orderBy');

		return $productsInStockQueryBuilder->getQuery()->getSingleScalarResult();
	}

}
