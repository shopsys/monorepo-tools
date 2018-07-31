<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search;

class CachedSearchClient implements SearchClient
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\SearchClient
     */
    protected $searchClient;

    /**
     * @var array
     */
    protected $cache;

    public function __construct(SearchClient $searchClient)
    {
        $this->searchClient = $searchClient;
        $this->cache = [];
    }

    /**
     * @param int $domainId
     * @param string|null $searchText
     * @return int[]
     */
    public function search(int $domainId, $searchText): array
    {
        if (!isset($this->cache[$domainId])) {
            $this->cache[$domainId] = [];
        }

        if (!isset($this->cache[$domainId][$searchText])) {
            $this->cache[$domainId][$searchText] = $this->searchClient->search($domainId, $searchText);
        }

        return $this->cache[$domainId][$searchText];
    }
}
