<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\DoctrineWalker\QueryBuilderService;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;

class ProductFilterRepository {

	/**
	 * @var \SS6\ShopBundle\Component\DoctrineWalker\QueryBuilderService
	 */
	private $queryBuilderService;

	public function __construct(QueryBuilderService $queryBuilderService) {
		$this->queryBuilderService = $queryBuilderService;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $queryBuilder
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $minimalPrice
	 * @param string $maximalPrice
	 */
	public function filterByPrice(
		QueryBuilder $queryBuilder,
		PricingGroup $pricingGroup,
		$minimalPrice,
		$maximalPrice
	) {
		$priceLimits = 'pcp.product = p AND pcp.pricingGroup = :pricingGroup';
		if ($minimalPrice !== null) {
			$priceLimits .= ' AND pcp.priceWithVat >= :minimalPrice';
			$queryBuilder->setParameter('minimalPrice', $minimalPrice);
		}
		if ($maximalPrice !== null) {
			$priceLimits .= ' AND pcp.priceWithVat <= :maximalPrice';
			$queryBuilder->setParameter('maximalPrice', $maximalPrice);
		}
		$this->queryBuilderService->addOrExtendJoin(
			$queryBuilder,
			ProductCalculatedPrice::class,
			'pcp',
			$priceLimits
		);
		$queryBuilder->setParameter('pricingGroup', $pricingGroup);

	}
}
