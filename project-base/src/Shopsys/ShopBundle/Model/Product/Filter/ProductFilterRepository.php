<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Availability\Availability;
use Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterRepository;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductCalculatedPrice;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductFilterRepository {

	const DAYS_FOR_STOCK_FILTER = 0;

	/**
	 * @var \Shopsys\ShopBundle\Component\Doctrine\QueryBuilderService
	 */
	private $queryBuilderService;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterRepository
	 */
	private $parameterFilterRepository;

	public function __construct(
		QueryBuilderService $queryBuilderService,
		ParameterFilterRepository $parameterFilterRepository
	) {
		$this->queryBuilderService = $queryBuilderService;
		$this->parameterFilterRepository = $parameterFilterRepository;
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	public function applyFiltering(
		QueryBuilder $productsQueryBuilder,
		ProductFilterData $productFilterData,
		PricingGroup $pricingGroup
	) {
		$this->filterByPrice(
			$productsQueryBuilder,
			$productFilterData->minimalPrice,
			$productFilterData->maximalPrice,
			$pricingGroup
		);
		$this->filterByStock(
			$productsQueryBuilder,
			$productFilterData->inStock
		);
		$this->filterByFlags(
			$productsQueryBuilder,
			$productFilterData->flags
		);
		$this->filterByBrands(
			$productsQueryBuilder,
			$productFilterData->brands
		);
		$this->parameterFilterRepository->filterByParameters(
			$productsQueryBuilder,
			$productFilterData->parameters
		);
	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param string $minimalPrice
	 * @param string $maximalPrice
	 * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 */
	private function filterByPrice(
		QueryBuilder $productsQueryBuilder,
		$minimalPrice,
		$maximalPrice,
		PricingGroup $pricingGroup
	) {
		if ($maximalPrice !== null || $minimalPrice !== null) {
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
			$productsQueryBuilder->andWhere('a.dispatchTime = :dispatchTime');
			$productsQueryBuilder->setParameter('dispatchTime', self::DAYS_FOR_STOCK_FILTER);
		}

	}

	/**
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \Shopsys\ShopBundle\Model\Product\Flag\Flag[] $flags
	 */
	private function filterByFlags(QueryBuilder $productsQueryBuilder, array $flags) {
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
	 * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
	 * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand[] $brands
	 */
	private function filterByBrands(QueryBuilder $productsQueryBuilder, array $brands) {
		$brandsCount = count($brands);
		if ($brandsCount !== 0) {
			$brandsQueryBuilder = $this->getBrandsQueryBuilder($brands, $productsQueryBuilder->getEntityManager());

			$productsQueryBuilder->andWhere($productsQueryBuilder->expr()->exists($brandsQueryBuilder));
			foreach ($brandsQueryBuilder->getParameters() as $parameter) {
				$productsQueryBuilder->setParameter($parameter->getName(), $parameter->getValue());
			}
		}
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Flag\Flag[] $flags
	 * @param \Doctrine\ORM\EntityManager $em
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getFlagsQueryBuilder(array $flags, EntityManager $em) {
		$flagsQueryBuilder = $em->createQueryBuilder();

		$flagsQueryBuilder
			->select('1')
			->from(Product::class, 'pf')
			->join('pf.flags', 'f')
			->where('pf = p')
			->andWhere('f IN (:flags)')->setParameter('flags', $flags);

		return $flagsQueryBuilder;
	}

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand[] $brands
	 * @param \Doctrine\ORM\EntityManager $em
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	private function getBrandsQueryBuilder(array $brands, EntityManager $em) {
		$brandsQueryBuilder = $em->createQueryBuilder();

		$brandsQueryBuilder
			->select('1')
			->from(Product::class, 'pb')
			->join('pb.brand', 'b')
			->where('pb = p')
			->andWhere('b IN (:brands)')->setParameter('brands', $brands);

		return $brandsQueryBuilder;
	}

}
