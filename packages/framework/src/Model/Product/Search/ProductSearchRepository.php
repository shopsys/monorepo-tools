<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient;

class ProductSearchRepository
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    protected $microserviceProductSearchClient;

    /**
     * @var int[][][]
     */
    protected $foundProductIdsCache = [];

    public function __construct(MicroserviceClient $microserviceProductSearchClient)
    {
        $this->microserviceProductSearchClient = $microserviceProductSearchClient;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
     * @param string|null $searchText
     */
    public function filterBySearchText(QueryBuilder $productQueryBuilder, $searchText)
    {
        $productIds = $this->getFoundProductIds($productQueryBuilder, $searchText);

        if (count($productIds) > 0) {
            $productQueryBuilder->andWhere('p.id IN (:productIds)')->setParameter('productIds', $productIds);
        } else {
            $productQueryBuilder->andWhere('TRUE = FALSE');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
     * @param string|null $searchText
     */
    public function addRelevance(QueryBuilder $productQueryBuilder, $searchText)
    {
        $productIds = $this->getFoundProductIds($productQueryBuilder, $searchText);

        if (count($productIds)) {
            $productQueryBuilder->addSelect('field(p.id, ' . implode(',', $productIds) . ') AS HIDDEN relevance');
        } else {
            $productQueryBuilder->addSelect('-1 AS HIDDEN relevance');
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $productQueryBuilder
     * @param $searchText
     * @return int[]
     */
    protected function getFoundProductIds(QueryBuilder $productQueryBuilder, $searchText)
    {
        $domainId = $productQueryBuilder->getParameter('domainId')->getValue();

        if (!isset($this->foundProductIdsCache[$domainId][$searchText])) {
            $resource = sprintf('%s/search-product-ids', $domainId);
            $searchResult = $this->microserviceProductSearchClient->get($resource, [
                'searchText' => $searchText,
            ]);
            $foundProductIds = $searchResult->productIds;

            $this->foundProductIdsCache[$domainId][$searchText] = $foundProductIds;
        }

        return $this->foundProductIdsCache[$domainId][$searchText];
    }
}
