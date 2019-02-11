<?php

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository;

class ProductSearchExporter
{
    const BATCH_SIZE = 100;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportRepository
     */
    protected $productSearchExportRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository
     */
    protected $productElasticsearchRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter
     */
    protected $productElasticsearchConverter;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportRepository $productSearchExportRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository $productElasticsearchRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter $productElasticsearchConverter
     */
    public function __construct(
        ProductSearchExportRepository $productSearchExportRepository,
        ProductElasticsearchRepository $productElasticsearchRepository,
        ProductElasticsearchConverter $productElasticsearchConverter
    ) {
        $this->productSearchExportRepository = $productSearchExportRepository;
        $this->productElasticsearchRepository = $productElasticsearchRepository;
        $this->productElasticsearchConverter = $productElasticsearchConverter;
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

        $data = $this->productElasticsearchConverter->convertExportBulk($productsData);
        $this->productElasticsearchRepository->bulkUpdate($domainId, $data);

        return $this->productElasticsearchConverter->extractIds($productsData);
    }

    /**
     * @param int $domainId
     * @param int[] $exportedIds
     */
    protected function removeNotUpdated(int $domainId, array $exportedIds): void
    {
        $this->productElasticsearchRepository->deleteNotPresent($domainId, $exportedIds);
    }
}
