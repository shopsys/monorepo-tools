<?php

namespace Shopsys\FrameworkBundle\Model\Product\ProductSearchExport;

use Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient;

class ProductSearchExportExporter
{
    const BATCH_SIZE = 100;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Microservice\MicroserviceClient
     */
    protected $client;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportRepository
     */
    protected $productSearchExportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductSearchExport\ProductSearchExportDataConverter
     */
    protected $productSearchExportDataConverter;

    public function __construct(
        MicroserviceClient $client,
        ProductSearchExportRepository $productSearchExportRepository,
        ProductSearchExportDataConverter $productSearchExportDataConverter
    ) {
        $this->client = $client;
        $this->productSearchExportRepository = $productSearchExportRepository;
        $this->productSearchExportDataConverter = $productSearchExportDataConverter;
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
        $productsData = $this->productSearchExportRepository->getProductsData($domainId, $locale, $startFrom, self::BATCH_SIZE);
        if (count($productsData) === 0) {
            return [];
        }

        $data = $this->productSearchExportDataConverter->convertBulk($productsData);

        $resource = sprintf('%s/products', $domainId);
        $this->client->patch($resource, $data);

        return $this->productSearchExportDataConverter->extractIds($productsData);
    }

    /**
     * @param string $index
     * @param int[] $exportedIds
     */
    protected function removeNotUpdated(string $index, array $exportedIds): void
    {
        $resource = sprintf('%s/products', $index);
        $data = [
            'keep' => $exportedIds,
        ];
        $this->client->delete($resource, $data);
    }
}
