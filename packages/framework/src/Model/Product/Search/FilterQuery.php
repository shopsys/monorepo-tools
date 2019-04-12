<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Listing\ProductListOrderingConfig;

class FilterQuery
{
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
}
