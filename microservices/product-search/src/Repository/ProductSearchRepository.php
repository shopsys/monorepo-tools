<?php

namespace Shopsys\MicroserviceProductSearch\Repository;

use Elasticsearch\Client;

class ProductSearchRepository
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    /**
     * @param \Elasticsearch\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getProductIdsBySearchText(int $domainId, string $searchText): array
    {
        if (!$searchText) {
            return [];
        }

        $parameters = $this->createQuery($domainId, $searchText);
        $result = $this->client->search($parameters);
        return $this->extractIds($result);
    }

    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-body.html
     * @param int $domainId
     * @param string $searchText
     * @return array
     */
    protected function createQuery(int $domainId, string $searchText): array
    {
        return [
            'index' => $domainId,
            'type' => '_doc',
            'size' => 1000,
            'body' => [
                '_source' => false,
                'query' => [
                    'multi_match' => [
                        'query' => $searchText,
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
                ],
            ],
        ];
    }

    /**
     * @param array $result
     * @return int[]
     */
    protected function extractIds(array $result): array
    {
        $hits = $result['hits']['hits'];
        return array_column($hits, '_id');
    }
}
