<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig;

class FilterQuery
{
    protected const MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT = 100;

    /** @var array */
    protected $filters = [];

    /** @var string */
    protected $indexName;

    /** @var array */
    protected $sorting = [];

    /** @var int */
    protected $limit = 1000;

    /** @var int */
    protected $page = 1;

    /** @var array */
    protected $match;

    /**
     * @param string $indexName
     */
    public function __construct(string $indexName)
    {
        $this->indexName = $indexName;
        $this->match = $this->matchAll();
    }

    /**
     * @param string $orderingModeId
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function applyOrdering(string $orderingModeId, PricingGroup $pricingGroup): self
    {
        $clone = clone $this;

        if ($orderingModeId === ProductListOrderingConfig::ORDER_BY_PRIORITY) {
            $clone->sorting = [
                'ordering_priority' => 'desc',
                'name.keyword' => 'asc',
            ];

            return $clone;
        }

        if ($orderingModeId === ProductListOrderingConfig::ORDER_BY_NAME_ASC) {
            $clone->sorting = [
                'name.keyword' => 'asc',
            ];

            return $clone;
        }

        if ($orderingModeId === ProductListOrderingConfig::ORDER_BY_NAME_DESC) {
            $clone->sorting = [
                'name.keyword' => 'desc',
            ];

            return $clone;
        }

        if ($orderingModeId === ProductListOrderingConfig::ORDER_BY_PRICE_ASC) {
            $clone->sorting = [
                'prices.amount' => [
                    'order' => 'asc',
                    'nested' => [
                        'path' => 'prices',
                        'filter' => [
                            'term' => [
                                'prices.pricing_group_id' => $pricingGroup->getId(),
                            ],
                        ],
                    ],
                ],
                'ordering_priority' => 'asc',
                'name.keyword' => 'asc',
            ];

            return $clone;
        }

        if ($orderingModeId === ProductListOrderingConfig::ORDER_BY_PRICE_DESC) {
            $clone->sorting = [
                'prices.amount' => [
                    'order' => 'desc',
                    'nested' => [
                        'path' => 'prices',
                        'filter' => [
                            'term' => [
                                'prices.pricing_group_id' => $pricingGroup->getId(),
                            ],
                        ],
                    ],
                ],
                'ordering_priority' => 'asc',
                'name.keyword' => 'desc',
            ];

            return $clone;
        }

        return $clone;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function applyDefaultOrdering(): self
    {
        $clone = clone $this;

        $clone->sorting = [
            'ordering_priority' => 'desc',
            'name.keyword' => 'asc',
        ];

        return $clone;
    }

    /**
     * @param array $parameters
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterByParameters(array $parameters): self
    {
        $clone = clone $this;

        foreach ($parameters as $parameterId => $parameterValues) {
            $clone->filters[] = [
                'nested' => [
                    'path' => 'parameters',
                    'query' => [
                        'bool' => [
                            'must' => [
                                'match_all' => new \stdClass(),
                            ],
                            'filter' => [
                                [
                                    'term' => [
                                        'parameters.parameter_id' => $parameterId,
                                    ],
                                ],
                                [
                                    'terms' => [
                                        'parameters.parameter_value_id' => $parameterValues,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $clone;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $minimalPrice
     * @param \Shopsys\FrameworkBundle\Component\Money\Money|null $maximalPrice
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterByPrices(PricingGroup $pricingGroup, Money $minimalPrice = null, Money $maximalPrice = null): self
    {
        $clone = clone $this;

        $prices = [];
        if ($minimalPrice !== null) {
            $prices['gte'] = (float)$minimalPrice->getAmount();
        }
        if ($maximalPrice !== null) {
            $prices['lte'] = (float)$maximalPrice->getAmount();
        }

        $clone->filters[] = [
            'nested' => [
                'path' => 'prices',
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => new \stdClass(),
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'prices.pricing_group_id' => $pricingGroup->getId(),
                                ],
                            ],
                            [
                                'range' => [
                                    'prices.amount' => $prices,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $clone;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterOnlyVisible(PricingGroup $pricingGroup): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'nested' => [
                'path' => 'prices',
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => new \stdClass(),
                        ],
                        'filter' => [
                            [
                                'term' => [
                                    'prices.pricing_group_id' => $pricingGroup->getId(),
                                ],
                            ],
                            [
                                'range' => [
                                    'prices.amount' => [
                                        'gt' => 0,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $clone;
    }

    /**
     * @param int[] $categoryIds
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterByCategory(array $categoryIds): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'terms' => [
                'categories' => $categoryIds,
            ],
        ];

        return $clone;
    }

    /**
     * @param int[] $brandIds
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterByBrands(array $brandIds): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'terms' => [
                'brand' => $brandIds,
            ],
        ];

        return $clone;
    }

    /**
     * @param int[] $flagIds
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterByFlags(array $flagIds): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'terms' => [
                'flags' => $flagIds,
            ],
        ];

        return $clone;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterOnlyInStock(): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'term' => [
                'in_stock' => true,
            ],
        ];

        return $clone;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function filterOnlySellable(): self
    {
        $clone = clone $this;

        $clone->filters[] = [
            'term' => [
                'calculated_selling_denied' => false,
            ],
        ];

        return $clone;
    }

    /**
     * @param string $text
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function search(string $text): self
    {
        $clone = clone $this;

        $clone->match = [
            'multi_match' => [
                'query' => $text,
                'fields' => [
                    'name.full_with_diacritic^60',
                    'name.full_without_diacritic^50',
                    'name^45',
                    'name.edge_ngram_with_diacritic^40',
                    'name.edge_ngram_without_diacritic^35',
                    'catnum^50',
                    'catnum.edge_ngram^25',
                    'partno^40',
                    'partno.edge_ngram^20',
                    'ean^60',
                    'ean.edge_ngram^30',
                    'short_description^5',
                    'description^5',
                ],
            ],
        ];

        return $clone;
    }

    /**
     * @param int $page
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function setPage(int $page): self
    {
        $clone = clone $this;

        $clone->page = $page;

        return $clone;
    }

    /**
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function setLimit(int $limit): self
    {
        $clone = clone $this;

        $clone->limit = $limit;

        return $clone;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        $query = [
            'index' => $this->indexName,
            'type' => '_doc',
            'body' => [
                'from' => $this->countFrom($this->page, $this->limit),
                'size' => $this->limit,
                'sort' => $this->sorting,
                'query' => [
                    'bool' => [
                        'must' => $this->match,
                        'filter' => $this->filters,
                    ],
                ],
            ],
        ];

        return $query;
    }

    /**
     * @return array
     */
    protected function matchAll(): array
    {
        return [
            'match_all' => new \stdClass(),
        ];
    }

    /**
     * @param int $page
     * @param int $limit
     * @return int
     */
    protected function countFrom(int $page, int $limit): int
    {
        return ($page - 1) * $limit;
    }

    /**
     * Applies all filters and calculate standard (non pluses) numbers
     * For flags, brands and stock
     *
     * @return array
     */
    public function getAbsoluteNumbersAggregationQuery(): array
    {
        return [
            'index' => $this->indexName,
            'type' => '_doc',
            'body' => [
                'size' => 0,
                'aggs' => [
                    'flags' => [
                        'terms' => [
                            'field' => 'flags',
                            'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                        ],
                    ],
                    'brands' => [
                        'terms' => [
                            'field' => 'brand',
                            'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                        ],
                    ],
                    'stock' => [
                        'filter' => [
                            'term' => [
                                'in_stock' => 'true',
                            ],
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => $this->match,
                        'filter' => $this->filters,
                    ],
                ],
            ],
        ];
    }

    /**
     * Applies all filters and calculate standard (non pluses) numbers
     * For flags, brands, stock, parameters
     * Parameters aggregation have nested structure in result [parameter_id][parameter_value_id]
     *
     * @return array
     */
    public function getAbsoluteNumbersWithParametersQuery(): array
    {
        $query = $this->getAbsoluteNumbersAggregationQuery();
        $query['body']['aggs']['parameters'] = [
            'nested' => [
                'path' => 'parameters',
            ],
            'aggs' => [
                'by_parameters' => [
                    'terms' => [
                        'field' => 'parameters.parameter_id',
                        'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                    ],
                    'aggs' => [
                        'by_value' => [
                            'terms' => [
                                'field' => 'parameters.parameter_value_id',
                                'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $query;
    }

    /**
     * Answers question "If I add this flag, how many products will be added?"
     * We are looking for count of products that meet all filters and don't have any of already selected flags
     *
     * @param int[] $selectedFlags
     * @return array
     */
    public function getFlagsPlusNumbersQuery(array $selectedFlags): array
    {
        return [
            'index' => $this->indexName,
            'type' => '_doc',
            'body' => [
                'size' => 0,
                'aggs' => [
                    'flags' => [
                        'terms' => [
                            'field' => 'flags',
                            'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => $this->match,
                        'filter' => $this->filters,
                        'must_not' => [
                            'terms' => [
                                'flags' => $selectedFlags,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Answers question "If I add this brand, how many products will be added?"
     * We are looking for count of products that meet all filters and don't have any of already selected brand
     *
     * @param int[] $selectedBrandsIds
     * @return array
     */
    public function getBrandsPlusNumbersQuery(array $selectedBrandsIds): array
    {
        return [
            'index' => $this->indexName,
            'type' => '_doc',
            'body' => [
                'size' => 0,
                'aggs' => [
                    'brands' => [
                        'terms' => [
                            'field' => 'brand',
                            'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => $this->match,
                        'filter' => $this->filters,
                        'must_not' => [
                            'terms' => [
                                'brand' => $selectedBrandsIds,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Answers question "If I add this parameter value, how many products will be added?"
     * We are looking for count of products that meet all filters and don't have already selected parameter value
     *
     * This query makes sense only within a single parameter, so it have to be executed for all parameters
     * (that have selected value and can have plus numbers)
     *
     * @param int $selectedParameterId
     * @param array $selectedValuesIds
     * @return array
     */
    public function getParametersPlusNumbersQuery(int $selectedParameterId, array $selectedValuesIds): array
    {
        return [
            'index' => $this->indexName,
            'type' => '_doc',
            'body' => [
                'size' => 0,
                'aggs' => [
                    'parameters' => [
                        'nested' => [
                            'path' => 'parameters',
                        ],
                        'aggs' => [
                            'filtered_for_parameter' => [
                                'filter' => [
                                    'term' => [
                                        'parameters.parameter_id' => $selectedParameterId,
                                    ],
                                ],
                                'aggs' => [
                                    'by_parameters' => [
                                        'terms' => [
                                            'field' => 'parameters.parameter_id',
                                            'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                                        ],
                                        'aggs' => [
                                            'by_value' => [
                                                'terms' => [
                                                    'field' => 'parameters.parameter_value_id',
                                                    'size' => static::MAXIMUM_REASONABLE_AGGREGATION_BUCKET_COUNT,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'query' => [
                    'bool' => [
                        'must' => $this->match,
                        'filter' => $this->filters,
                        'must_not' => [
                            [
                                'nested' => [
                                    'path' => 'parameters',
                                    'query' => [
                                        'bool' => [
                                            'must' => [
                                                'terms' => [
                                                    'parameters.parameter_value_id' => $selectedValuesIds,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
