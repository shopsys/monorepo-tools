<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search;

interface SearchClient
{
    /**
     * @param int $domainId
     * @param string|null $searchText
     * @return int[]
     */
    public function search(int $domainId, $searchText): array;
}
