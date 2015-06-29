<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterData;
use SS6\ShopBundle\Model\Product\Filter\ProductFilterRepository;
use SS6\ShopBundle\Model\Product\Product;
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
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	public function getProductFilterCountDataInCategory(
		Category $category,
		$domainId,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
			$domainId,
			$pricingGroup,
			$category
		);

		return $this->getProductFilterCountData(
			$productsQueryBuilder,
			$productFilterData,
			$pricingGroup
		);
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
		$productsQueryBuilder = $this->productRepository->getListableBySearchTextQueryBuilder(
			$domainId,
			$pricingGroup,
			$locale,
			$searchText
		);

		return $this->getProductFilterCountData(
			$productsQueryBuilder,
			$productFilterData,
			$pricingGroup
		);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Filter\ProductFilterCountData
	 */
	private function getProductFilterCountData(
		QueryBuilder $productsQueryBuilder,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productFilterCountData = new ProductFilterCountData();
		$productFilterCountData->countInStock = $this->getCountInStock(
			$productsQueryBuilder,
			$productFilterData,
			$pricingGroup
		);
		$productFilterCountData->countByFlagId = $this->getCountByFlagId(
			$productsQueryBuilder,
			$productFilterData,
			$pricingGroup
		);

		return $productFilterCountData;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return int
	 */
	private function getCountInStock(
		QueryBuilder $productsQueryBuilder,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productsInStockQueryBuilder = clone $productsQueryBuilder;
		$productInStockFilterData = clone $productFilterData;

		$productInStockFilterData->inStock = true;

		$this->productFilterRepository->applyFiltering(
			$productsInStockQueryBuilder,
			$productInStockFilterData,
			$pricingGroup
		);

		$productsInStockQueryBuilder
			->select('COUNT(p)')
			->resetDQLPart('orderBy');

		return $productsInStockQueryBuilder->getQuery()->getSingleScalarResult();
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return int
	 */
	private function getCountByFlagId(
		QueryBuilder $productsQueryBuilder,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$productFilterDataExceptFlags = clone $productFilterData;
		$productFilterDataExceptFlags->flags = [];

		$productsFilteredExceptFlagsQueryBuilder = clone $productsQueryBuilder;

		$this->productFilterRepository->applyFiltering(
			$productsFilteredExceptFlagsQueryBuilder,
			$productFilterDataExceptFlags,
			$pricingGroup
		);

		$productsFilteredExceptFlagsQueryBuilder
			->select('f.id, COUNT(p) AS cnt')
			->join('p.flags', 'f')
			->andWhere(
				$productsFilteredExceptFlagsQueryBuilder->expr()->notIn(
					'p.id',
					$this->em->createQueryBuilder()
						->select('_p.id')
						->from(Product::class, '_p')
						->join('_p.flags', '_f')
						->where('_f IN (:activeFlags)')
						->getDQL()
				)
			)
			->resetDQLPart('orderBy')
			->groupBy('f.id')
			->setParameter('activeFlags', $productFilterData->flags);

		$rows = $productsFilteredExceptFlagsQueryBuilder->getQuery()->execute();

		$countByFlagId = [];
		foreach ($rows as $row) {
			$countByFlagId[$row['id']] = $row['cnt'];
		}

		return $countByFlagId;
	}

}
