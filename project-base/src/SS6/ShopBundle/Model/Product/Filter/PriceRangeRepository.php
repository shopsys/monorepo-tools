<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Doctrine\QueryBuilderService;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Filter\PriceRange;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\ProductRepository;

class PriceRangeRepository {

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\QueryBuilderService
	 */
	private $queryBuilderService;

	public function __construct(ProductRepository $productRepository, QueryBuilderService $queryBuilderService) {
		$this->productRepository = $productRepository;
		$this->queryBuilderService = $queryBuilderService;
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Product\Filter\PriceRange
	 */
	public function getPriceRangeInCategory($domainId, PricingGroup $pricingGroup, Category $category) {
		$productsQueryBuilder = $this->productRepository->getListableInCategoryQueryBuilder(
			$domainId,
			$pricingGroup,
			$category
		);

		return $this->getPriceRangeByProductsQueryBuilder($productsQueryBuilder, $pricingGroup);
	}

	/**
	 * @param int $domainId
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $locale
	 * @param string|null $searchText
	 * @return \SS6\ShopBundle\Model\Product\Filter\PriceRange
	 */
	public function getPriceRangeForSearch($domainId, PricingGroup $pricingGroup, $locale, $searchText) {
		$productsQueryBuilder = $this->productRepository
			->getListableBySearchTextQueryBuilder($domainId, $pricingGroup, $locale, $searchText);

		return $this->getPriceRangeByProductsQueryBuilder($productsQueryBuilder, $pricingGroup);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Product\Filter\PriceRange
	 */
	private function getPriceRangeByProductsQueryBuilder(QueryBuilder $productsQueryBuilder, PricingGroup $pricingGroup) {
		$queryBuilder = clone $productsQueryBuilder;

		$this->queryBuilderService->addOrExtendJoin(
			$queryBuilder,
			ProductCalculatedPrice::class,
			'pcp',
			'pcp.product = p AND pcp.pricingGroup = :pricingGroup'
		);

		$queryBuilder
			->select('MIN(pcp.priceWithVat) AS minimalPrice, MAX(pcp.priceWithVat) AS maximalPrice')
			->setParameter('pricingGroup', $pricingGroup)
			->resetDQLPart('groupBy')
			->resetDQLPart('orderBy');

		$priceRangeData = $queryBuilder->getQuery()->execute();
		$priceRangeDataRow = reset($priceRangeData);

		return new PriceRange($priceRangeDataRow['minimalPrice'], $priceRangeDataRow['maximalPrice']);
	}

}
