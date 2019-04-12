<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Brand\Brand;
use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData;
use Shopsys\FrameworkBundle\Model\Product\Flag\Flag;
use Shopsys\FrameworkBundle\Model\Product\Parameter\ParameterValue;

class ProductFilterDataToQueryTransformer
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function addBrandsToQuery(ProductFilterData $productFilterData, FilterQuery $filterQuery): FilterQuery
    {
        if (count($productFilterData->brands) === 0) {
            return $filterQuery;
        }

        $brandIds = \array_map(
            static function (Brand $brand) {
                return $brand->getId();
            },
            $productFilterData->brands
        );

        return $filterQuery->filterByBrands($brandIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function addFlagsToQuery(ProductFilterData $productFilterData, FilterQuery $filterQuery): FilterQuery
    {
        if (count($productFilterData->flags) === 0) {
            return $filterQuery;
        }

        $flagIds = \array_map(
            static function (Flag $flag) {
                return $flag->getId();
            },
            $productFilterData->flags
        );

        return $filterQuery->filterByFlags($flagIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function addParametersToQuery(ProductFilterData $productFilterData, FilterQuery $filterQuery): FilterQuery
    {
        if (count($productFilterData->parameters) === 0) {
            return $filterQuery;
        }

        $parameters = $this->flattenParameterFilterData($productFilterData->parameters);

        return $filterQuery->filterByParameters($parameters);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ParameterFilterData[] $parameters
     * @return array
     */
    protected function flattenParameterFilterData(array $parameters): array
    {
        $result = [];

        foreach ($parameters as $parameterFilterData) {
            if (\count($parameterFilterData->values) === 0) {
                continue;
            }

            $result[$parameterFilterData->parameter->getId()] =
                \array_map(
                    static function (ParameterValue $item) {
                        return $item->getId();
                    },
                    $parameterFilterData->values
                );
        }

        return $result;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function addStockToQuery(ProductFilterData $productFilterData, FilterQuery $filterQuery): FilterQuery
    {
        if ($productFilterData->inStock === false) {
            return $filterQuery;
        }

        return $filterQuery->filterOnlyInStock();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterData $productFilterData
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery $filterQuery
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function addPricesToQuery(ProductFilterData $productFilterData, FilterQuery $filterQuery, PricingGroup $pricingGroup): FilterQuery
    {
        if ($productFilterData->maximalPrice === null && $productFilterData->minimalPrice === null) {
            return $filterQuery;
        }

        return $filterQuery->filterByPrices($pricingGroup, $productFilterData->minimalPrice, $productFilterData->maximalPrice);
    }
}
