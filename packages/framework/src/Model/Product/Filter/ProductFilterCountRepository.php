<?php

namespace Shopsys\FrameworkBundle\Model\Product\Filter;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ProductParameterValue;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductFilterCountRepository
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterRepository
     */
    private $productFilterRepository;

    public function __construct(
        EntityManagerInterface $em,
        ProductFilterRepository $productFilterRepository
    ) {
        $this->em = $em;
        $this->productFilterRepository = $productFilterRepository;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param string $locale
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterConfig $productFilterConfig
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function getProductFilterCountData(
        QueryBuilder $productsQueryBuilder,
        $locale,
        ProductFilterConfig $productFilterConfig,
        ProductFilterData $productFilterData,
        PricingGroup $pricingGroup
    ) {
        $productFilterCountData = new ProductFilterCountData();
        $productFilterCountData->countInStock = $this->getCountInStock(
            $productsQueryBuilder,
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByBrandId = $this->getCountIndexedByBrandId(
            $productsQueryBuilder,
            $productFilterConfig->getBrandChoices(),
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByFlagId = $this->getCountIndexedByFlagId(
            $productsQueryBuilder,
            $productFilterConfig->getFlagChoices(),
            $productFilterData,
            $pricingGroup
        );
        $productFilterCountData->countByParameterIdAndValueId = $this->getCountIndexedByParameterIdAndValueId(
            $productsQueryBuilder,
            $productFilterConfig->getParameterChoices(),
            $productFilterData,
            $pricingGroup,
            $locale
        );

        return $productFilterCountData;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productsQueryBuilder
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Brand\Brand[] $brandFilterChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return int[]
     */
    private function getCountIndexedByBrandId(
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Flag\Flag[] $flagFilterChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return int[]
     */
    private function getCountIndexedByFlagId(
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
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterChoice[] $parameterFilterChoices
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string $locale
     * @return int[][]
     */
    private function getCountIndexedByParameterIdAndValueId(
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
