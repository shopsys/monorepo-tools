<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

use Elasticsearch\Client;

class ElasticsearchProductExporter
{
    const BATCH_SIZE = 100;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductRepository
     */
    protected $elasticsearchProductRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductDataConverter
     */
    protected $elasticsearchProductDataConverter;

    public function __construct(
        Client $client,
        ElasticsearchProductRepository $elasticsearchProductRepository,
        ElasticsearchProductDataConverter $elasticsearchProductDataConverter
    ) {
        $this->client = $client;
        $this->elasticsearchProductRepository = $elasticsearchProductRepository;
        $this->elasticsearchProductDataConverter = $elasticsearchProductDataConverter;
    }

    /**
     * @param int $domainId
     * @param string $locale
     */
    public function export(int $domainId, string $locale): void
    {
        $startFrom = 0;
        $exportedIds = [];
        do {
            $batchExportedIds = $this->exportBatch($domainId, $locale, $startFrom);
            $exportedIds = array_merge($exportedIds, $batchExportedIds);
            $startFrom += self::BATCH_SIZE;
        } while (!empty($batchExportedIds));
        $this->removeNotUpdated((string)$domainId, $exportedIds);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int $startFrom
     * @return int[]
     */
    protected function exportBatch(int $domainId, string $locale, int $startFrom): array
    {
        $productsData = $this->elasticsearchProductRepository->getProductsData($domainId, $locale, $startFrom, self::BATCH_SIZE);
        if (count($productsData) === 0) {
            return [];
        }

        $data = $this->elasticsearchProductDataConverter->convertBulk((string)$domainId, $productsData);
        $params = [
            'body' => $data,
        ];
        $this->client->bulk($params);

        return $this->elasticsearchProductDataConverter->extractIds($productsData);
    }

    /**
     * @param string $index
     * @param int[] $exportedIds
     */
    protected function removeNotUpdated(string $index, array $exportedIds): void
    {
        $this->client->deleteByQuery([
            'index' => $index,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'ids' => [
                                'values' => $exportedIds,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
