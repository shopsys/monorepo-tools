<?php

namespace Shopsys\MicroserviceProductSearchExport\Repository;

use Elasticsearch\Client;

class ProductRepository
{
    /**
     * @var \Shopsys\MicroserviceProductSearchExport\Repository\ElasticsearchProductConverter
     */
    protected $converter;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @param \Shopsys\MicroserviceProductSearchExport\Repository\ElasticsearchProductConverter $converter
     * @param \Elasticsearch\Client $client
     */
    public function __construct(ElasticsearchProductConverter $converter, Client $client)
    {
        $this->converter = $converter;
        $this->client = $client;
    }

    /**
     * @param int $domainId
     * @param array $data
     */
    public function bulkUpdate(int $domainId, array $data): void
    {
        $body = $this->converter->convertBulk($domainId, $data);

        $params = [
            'body' => $body,
        ];
        $this->client->bulk($params);
    }

    /**
     * @param int $domainId
     * @param int[] $keepIds
     */
    public function deleteNotPresent(int $domainId, array $keepIds): void
    {
        $this->client->deleteByQuery([
            'index' => $domainId,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'ids' => [
                                'values' => $keepIds,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
