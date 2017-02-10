<?php

namespace Shopsys\ShopBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountData;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\ShopBundle\Model\Product\Filter\ProductFilterRepository;
use Shopsys\ShopBundle\Model\Product\Parameter\ProductParameterValue;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductFilterCountRepository
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
     */
    private $productRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterRepository
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
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param string $locale
     * @param \Shopsys\ShopBundle\Form\Front\Product\ProductFilterFormType $productFilterFormType
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountData(
        QueryBuilder $productsQueryBuilder,
        $locale,
        ProductFilterFormType $productFilterFormType,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
    ) {
        $productFilterCountData = new ProductFilterCountData();
        $productFilterCountData->countInStock = $this->getCountInStock(
            $productsQueryBuilder,
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByBrandId = $this->getCountByBrandId(
            $productsQueryBuilder,
            $productFilterFormType->getBrandFilterChoices(),
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByFlagId = $this->getCountByFlagId(
            $productsQueryBuilder,
            $productFilterFormType->getFlagFilterChoices(),
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByParameterIdAndValueId = $this->getCountByParameterIdAndValueId(
            $productsQueryBuilder,
            $productFilterFormType->getParameterFilterChoices(),
            $productFilterData,
            $pricingGroup,
            $locale
        );

        return $productFilterCountData;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
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
     * @param \Shopsys\ShopBundle\Model\Product\Brand\Brand[] $brandFilterChoices
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return int[brandId]
     */
    private function getCountByBrandId(
        QueryBuilder $productsQueryBuilder,
        array $brandFilterChoices,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
        ) {
        if (count($brandFilterChoices) === 0) {
            return [];
        }

        $productFilterDataExceptBrands = clone $productFilterData;
        $productFilterDataExceptBrands->brands = [];

        $productsFilteredExceptBrandsQueryBuilder = clone $productsQueryBuilder;

        $this->productFilterRepository->applyFiltering(
            $productsFilteredExceptBrandsQueryBuilder,
            $productFilterDataExceptBrands,
            $pricingGroup
        );

        $productsFilteredExceptBrandsQueryBuilder
            ->select('b.id, COUNT(p) AS cnt')
            ->join('p.brand', 'b')
            ->andWhere('b IN (:filterBrands)')->setParameter('filterBrands', $brandFilterChoices);

        if (count($productFilterData->brands) > 0) {
            $productsFilteredExceptBrandsQueryBuilder
                ->andWhere('p.brand NOT IN (:activeBrands)')
                ->setParameter('activeBrands', $productFilterData->brands);
        }

        $productsFilteredExceptBrandsQueryBuilder
            ->resetDQLPart('orderBy')
            ->groupBy('b.id');

        $rows = $productsFilteredExceptBrandsQueryBuilder->getQuery()->execute();

        $countByBrandId = [];
        foreach ($rows as $row) {
            $countByBrandId[$row['id']] = $row['cnt'];
        }

        return $countByBrandId;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param \Shopsys\ShopBundle\Model\Product\Flag\Flag[] $flagFilterChoices
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return int[flagId]
     */
    private function getCountByFlagId(
        QueryBuilder $productsQueryBuilder,
        array $flagFilterChoices,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
    ) {
        if (count($flagFilterChoices) === 0) {
            return [];
        }

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
            ->andWhere('f IN (:filterFlags)')->setParameter('filterFlags', $flagFilterChoices);

        if (count($productFilterData->flags) > 0) {
            $productsFilteredExceptFlagsQueryBuilder
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
                ->setParameter('activeFlags', $productFilterData->flags);
        }

        $productsFilteredExceptFlagsQueryBuilder
            ->resetDQLPart('orderBy')
            ->groupBy('f.id');

        $rows = $productsFilteredExceptFlagsQueryBuilder->getQuery()->execute();

        $countByFlagId = [];
        foreach ($rows as $row) {
            $countByFlagId[$row['id']] = $row['cnt'];
        }

        return $countByFlagId;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterFilterChoices
     * @param \Shopsys\ShopBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @return int[parameterId][valueId]
     */
    private function getCountByParameterIdAndValueId(
        QueryBuilder $productsQueryBuilder,
        array $parameterFilterChoices,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup,
        $locale
    ) {
        $countByParameterIdAndValueId = [];

        foreach ($parameterFilterChoices as $parameterFilterChoice) {
            $currentParameter = $parameterFilterChoice->getParameter();

            $productFilterDataExceptCurrentParameter = clone $productFilterData;
            foreach ($productFilterDataExceptCurrentParameter->parameters as $index => $parameterFilterData) {
                if ($parameterFilterData->parameter->getId() === $currentParameter->getId()) {
                    unset($productFilterDataExceptCurrentParameter->parameters[$index]);
                }
            }

            $productsFilteredExceptCurrentParameterQueryBuilder = clone $productsQueryBuilder;

            $this->productFilterRepository->applyFiltering(
                $productsFilteredExceptCurrentParameterQueryBuilder,
                $productFilterDataExceptCurrentParameter,
                $pricingGroup
            );

            $productsFilteredExceptCurrentParameterQueryBuilder
                ->select('pv.id, COUNT(p) AS cnt')
                ->join(ProductParameterValue::class, 'ppv', Join::WITH, 'ppv.product = p')
                ->join('ppv.value', 'pv', Join::WITH, 'pv.locale = :locale')
                ->andWhere('ppv.parameter = :parameter')
                ->resetDQLPart('orderBy')
                ->groupBy('pv.id')
                ->setParameter('locale', $locale)
                ->setParameter('parameter', $currentParameter);

            $rows = $productsFilteredExceptCurrentParameterQueryBuilder->getQuery()->execute();

            $countByParameterIdAndValueId[$currentParameter->getId()] = [];
            foreach ($rows as $row) {
                $countByParameterIdAndValueId[$currentParameter->getId()][$row['id']] = $row['cnt'];
            }
        }

        return $countByParameterIdAndValueId;
    }
}
