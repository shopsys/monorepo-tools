<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData;

class AggregationResultToProductFilterCountDataTransformer
{
    /**
     * @param array $aggregationResult
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function translateAbsoluteNumbers(array $aggregationResult): ProductFilterCountData
    {
        $countData = new ProductFilterCountData();
        $countData->countByFlagId = $this->getFlagCount($aggregationResult);
        $countData->countByBrandId = $this->getBrandCount($aggregationResult);
        $countData->countInStock = $this->getStockCount($aggregationResult);

        return $countData;
    }

    /**
     * @param array $aggregationResult
     * @return int[]
     */
    protected function getFlagCount(array $aggregationResult): array
    {
        $result = [];

        $flagsBucket = $aggregationResult['aggregations']['flags']['buckets'];
        foreach ($flagsBucket as $flagBucket) {
            $flagId = $flagBucket['key'];
            $flagCount = $flagBucket['doc_count'];
            $result[$flagId] = $flagCount;
        }
        return $result;
    }

    /**
     * @param array $aggregationResult
     * @return int[]
     */
    protected function getBrandCount(array $aggregationResult): array
    {
        $result = [];

        $brandsBucket = $aggregationResult['aggregations']['brands']['buckets'];
        foreach ($brandsBucket as $brandBucket) {
            $brandId = $brandBucket['key'];
            $brandCount = $brandBucket['doc_count'];
            $result[$brandId] = $brandCount;
        }
        return $result;
    }

    /**
     * @param array $aggregationResult
     * @return int
     */
    protected function getStockCount(array $aggregationResult): int
    {
        return $aggregationResult['aggregations']['stock']['doc_count'];
    }

    /**
     * @param array $aggregationResult
     * @return \Shopsys\FrameworkBundle\Model\Product\Filter\ProductFilterCountData
     */
    public function translateAbsoluteNumbersWithParameters(array $aggregationResult): ProductFilterCountData
    {
        $countData = $this->translateAbsoluteNumbers($aggregationResult);
        $countData->countByParameterIdAndValueId = $this->getParametersCount($aggregationResult);

        return $countData;
    }

    /**
     * @param array $aggregationResult
     * @return int[][]
     */
    protected function getParametersCount(array $aggregationResult): array
    {
        $result = [];

        $parametersBucket = $aggregationResult['aggregations']['parameters']['by_parameters']['buckets'];
        foreach ($parametersBucket as $parameterBucket) {
            $parameterId = $parameterBucket['key'];
            $result[$parameterId] = $this->getValuesCount($parameterBucket);
        }
        return $result;
    }

    /**
     * @param array $parameterBucket
     * @return int[]
     */
    protected function getValuesCount(array $parameterBucket): array
    {
        $valueBuckets = $parameterBucket['by_value']['buckets'];
        $values = [];
        foreach ($valueBuckets as $valueBucket) {
            $valueKey = $valueBucket['key'];
            $valueCount = $valueBucket['doc_count'];
            $values[$valueKey] = $valueCount;
        }
        return $values;
    }

    /**
     * @param array $aggregationResult
     * @return int[]
     */
    public function translateFlagsPlusNumbers(array $aggregationResult): array
    {
        $result = [];
        $flagsBucket = $aggregationResult['aggregations']['flags']['buckets'];
        foreach ($flagsBucket as $flagBucket) {
            $flagId = $flagBucket['key'];
            $flagCount = $flagBucket['doc_count'];
            $result[$flagId] = $flagCount;
        }
        return $result;
    }

    /**
     * @param array $aggregationResult
     * @return int[]
     */
    public function translateBrandsPlusNumbers(array $aggregationResult): array
    {
        $result = [];
        $brandsBucket = $aggregationResult['aggregations']['brands']['buckets'];
        foreach ($brandsBucket as $flagBucket) {
            $brandId = $flagBucket['key'];
            $brandCount = $flagBucket['doc_count'];
            $result[$brandId] = $brandCount;
        }
        return $result;
    }

    /**
     * @param array $aggregationResult
     * @return int[]
     */
    public function translateParameterValuesPlusNumbers(array $aggregationResult): array
    {
        $parametersBuckets = $aggregationResult['aggregations']['parameters']['filtered_for_parameter']['by_parameters']['buckets'];
        if (empty($parametersBuckets)) {
            return [];
        }
        $thePossibleBucket = $parametersBuckets[0];

        $result = [];
        foreach ($thePossibleBucket['by_value']['buckets'] as $bucket) {
            $valueId = $bucket['key'];
            $valueCount = $bucket['doc_count'];
            $result[$valueId] = $valueCount;
        }
        return $result;
    }
}
