<?php

namespace SS6\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use SS6\ShopBundle\Component\Doctrine\QueryBuilderService;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use SS6\ShopBundle\Model\Product\Product;

class ProductFilterRepository {

	const DAYS_FOR_STOCK_FILTER = 0;

	/**
	 * @var \SS6\ShopBundle\Component\Doctrine\QueryBuilderService
	 */
	private $queryBuilderService;

	public function __construct(
		QueryBuilderService $queryBuilderService
	) {
		$this->queryBuilderService = $queryBuilderService;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @param string $minimalPrice
	 * @param string $maximalPrice
	 */
	public function filterByPrice(
		QueryBuilder $productsQueryBuilder,
		PricingGroup $pricingGroup,
		$minimalPrice,
		$maximalPrice
	) {
		$priceLimits = 'pcp.product = p AND pcp.pricingGroup = :pricingGroup';
		if ($minimalPrice !== null) {
			$priceLimits .= ' AND pcp.priceWithVat >= :minimalPrice';
			$productsQueryBuilder->setParameter('minimalPrice', $minimalPrice);
		}
		if ($maximalPrice !== null) {
			$priceLimits .= ' AND pcp.priceWithVat <= :maximalPrice';
			$productsQueryBuilder->setParameter('maximalPrice', $maximalPrice);
		}
		$this->queryBuilderService->addOrExtendJoin(
			$productsQueryBuilder,
			ProductCalculatedPrice::class,
			'pcp',
			$priceLimits
		);
		$productsQueryBuilder->setParameter('pricingGroup', $pricingGroup);

	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param bool $filterByStock
	 */
	public function filterByStock(QueryBuilder $productsQueryBuilder, $filterByStock) {
		if ($filterByStock) {
			$this->queryBuilderService->addOrExtendJoin(
				$productsQueryBuilder,
				Availability::class,
				'a',
				'p.calculatedAvailability = a'
			);
			$productsQueryBuilder->andWhere('a.deliveryTime = :deliveryTime');
			$productsQueryBuilder->setParameter('deliveryTime', self::DAYS_FOR_STOCK_FILTER);
		}

	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 */
	public function filterByFlags(QueryBuilder $productsQueryBuilder, array $flags) {
		$flagsCount = count($flags);
		if ($flagsCount !== 0) {
			$flagsQueryBuilder = $this->getFlagsQueryBuilder($flags, $productsQueryBuilder->getEntityManager());

			$productsQueryBuilder->andWhere($productsQueryBuilder->expr()->exists($flagsQueryBuilder));
			foreach ($flagsQueryBuilder->getParameters() as $parameter) {
				$productsQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Product\Flag\Flag[] $flags
	 * @param \Doctrine\ORM\EntityManager $em
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getFlagsQueryBuilder(array $flags, EntityManager $em) {
		$flagsQueryBuilder = $em->createQueryBuilder();

		$orExpr = $flagsQueryBuilder->expr()->orX();

		$index = 0;
		foreach ($flags as $flag) {
			$orExpr->add('f = :flag' . $index);
			$flagsQueryBuilder->setParameter('flag' . $index, $flag);
			$index++;
		}

		$flagsQueryBuilder
			->select('1')
			->from(Product::class, 'pf')
			->join('p.flags', 'f', Join::ON)
			->where('pf = p')
			->andWhere($orExpr);

		return $flagsQueryBuilder;
	}

}
