<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search;

class FilterQueryFactory
{
    /**
     * @param string $indexName
     * @return \Shopsys\FrameworkBundle\Model\Product\Search\FilterQuery
     */
    public function create(string $indexName): FilterQuery
    {
        return new FilterQuery($indexName);
    }
}
