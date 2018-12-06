<?php

namespace Shopsys\MicroserviceProductSearchExport\Repository;

use Elasticsearch\Client;
use Shopsys\MicroserviceProductSearchExport\Structure\StructureManager;

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
     * @var \Shopsys\MicroserviceProductSearchExport\Structure\StructureManager
     */
    protected $structureManager;

    /**
     * @param \Shopsys\MicroserviceProductSearchExport\Repository\ElasticsearchProductConverter $converter
     * @param \Elasticsearch\Client $client
     * @param \Shopsys\MicroserviceProductSearchExport\Structure\StructureManager $structureManager
     */
    public function __construct(ElasticsearchProductConverter $converter, Client $client, StructureManager $structureManager)
    {
        $this->converter = $converter;
        $this->client = $client;
        $this->structureManager = $structureManager;
    }

    /**
     * @param int $domainId
     * @param array $data
     */
    public function bulkUpdate(int $domainId, array $data): void
    {
        $body = $this->converter->convertBulk($this->structureManager->getIndexName($domainId), $data);

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
            'index' => $this->structureManager->getIndexName($domainId),
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
