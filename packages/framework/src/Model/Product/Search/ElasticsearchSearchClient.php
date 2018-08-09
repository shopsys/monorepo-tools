<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search;

use Elasticsearch\Client;

class ElasticsearchSearchClient implements SearchClient
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param int $domainId
     * @param string|null $searchText
     * @return int[]
     */
    public function search(int $domainId, $searchText): array
    {
        if (!$searchText) {
            return [];
        }

        $parameters = $this->createQuery($domainId, $searchText);
        $result = $this->client->search($parameters);
        return $this->extractIds($result);
    }

    /**
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
