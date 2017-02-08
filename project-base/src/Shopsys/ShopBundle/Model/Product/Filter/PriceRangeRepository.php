<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Filter\PriceRange;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class PriceRangeRepository {

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService
	 */
	private $queryBuilderService;

	public function __construct(ProductRepository $productRepository, QueryBuilderService $queryBuilderService) {
		$this->productRepository = $productRepository;
		$this->queryBuilderService = $queryBuilderService;
	}

	/**
	 * @param int $domainId
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @return \Shopsys\ShopBundle\Model\Product\Filter\PriceRange
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
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $locale
	 * @param string|null $searchText
	 * @return \Shopsys\ShopBundle\Model\Product\Filter\PriceRange
	 */
	public function getPriceRangeForSearch($domainId, PricingGroup $pricingGroup, $locale, $searchText) {
		$productsQueryBuilder = $this->productRepository
			->getListableBySearchTextQueryBuilder($domainId, $pricingGroup, $locale, $searchText);

		return $this->getPriceRangeByProductsQueryBuilder($productsQueryBuilder, $pricingGroup);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \Shopsys\ShopBundle\Model\Product\Filter\PriceRange
	 */
	private function getPriceRangeByProductsQueryBuilder(QueryBuilder $productsQueryBuilder, PricingGroup $pricingGroup) {
		$queryBuilder = clone $productsQueryBuilder;

		$this->queryBuilderService
			->addOrExtendJoin($queryBuilder, ProductCalculatedPrice::class, 'pcp', 'pcp.product = p')
			->andWhere('pcp.pricingGroup = :pricingGroup')
			->setParameter('pricingGroup', $pricingGroup)
			->resetDQLPart('groupBy')
			->resetDQLPart('orderBy')
			->select('MIN(pcp.priceWithVat) AS minimalPrice, MAX(pcp.priceWithVat) AS maximalPrice');

		$priceRangeData = $queryBuilder->getQuery()->execute();
		$priceRangeDataRow = reset($priceRangeData);

		return new PriceRange($priceRangeDataRow['minimalPrice'], $priceRangeDataRow['maximalPrice']);
	}

}
