<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Search\Export;

use BadMethodCallException;
use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory;
use Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter;
use Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProductSearchExporter
{
    /** @access protected */
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
     * @var \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory
     */
    protected $progressBarFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade
     */
    protected $sqlLoggerFacade;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\Export\ProductSearchExportRepository $productSearchExportRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchRepository $productElasticsearchRepository
     * @param \Shopsys\FrameworkBundle\Model\Product\Search\ProductElasticsearchConverter $productElasticsearchConverter
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory|null $progressBarFactory
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade|null $sqlLoggerFacade
     * @param \Doctrine\ORM\EntityManagerInterface|null $entityManager
     */
    public function __construct(
        ProductSearchExportRepository $productSearchExportRepository,
        ProductElasticsearchRepository $productElasticsearchRepository,
        ProductElasticsearchConverter $productElasticsearchConverter,
        ?ProgressBarFactory $progressBarFactory = null,
        ?SqlLoggerFacade $sqlLoggerFacade = null,
        ?EntityManagerInterface $entityManager = null
    ) {
        $this->productSearchExportRepository = $productSearchExportRepository;
        $this->productElasticsearchRepository = $productElasticsearchRepository;
        $this->productElasticsearchConverter = $productElasticsearchConverter;
        $this->progressBarFactory = $progressBarFactory;
        $this->sqlLoggerFacade = $sqlLoggerFacade;
        $this->entityManager = $entityManager;
    }

    /**
     * @required
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory $progressBarFactory
     * @deprecated Will be replaced with constructor injection in the next major release
     */
    public function setProgressBarFactory(ProgressBarFactory $progressBarFactory): void
    {
        if ($this->progressBarFactory !== null && $this->progressBarFactory !== $progressBarFactory) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        if ($this->progressBarFactory === null) {
            @trigger_error(sprintf('The %s() method is deprecated and will be removed in the next major. Use the constructor injection instead.', __METHOD__), E_USER_DEPRECATED);

            $this->progressBarFactory = $progressBarFactory;
        }
    }

    /**
     * @required
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @deprecated Will be replaced with constructor injection in the next major release
     */
    public function setSqlLoggerFacade(SqlLoggerFacade $sqlLoggerFacade): void
    {
        if ($this->sqlLoggerFacade !== null && $this->sqlLoggerFacade !== $sqlLoggerFacade) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        if ($this->sqlLoggerFacade === null) {
            @trigger_error(sprintf('The %s() method is deprecated and will be removed in the next major. Use the constructor injection instead.', __METHOD__), E_USER_DEPRECATED);

            $this->sqlLoggerFacade = $sqlLoggerFacade;
        }
    }

    /**
     * @required
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @deprecated Will be replaced with constructor injection in the next major release
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        if ($this->entityManager !== null && $this->entityManager !== $entityManager) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        if ($this->entityManager === null) {
            @trigger_error(sprintf('The %s() method is deprecated and will be removed in the next major. Use the constructor injection instead.', __METHOD__), E_USER_DEPRECATED);

            $this->entityManager = $entityManager;
        }
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @deprecated Use `exportWithOutput` instead
     */
    public function export(int $domainId, string $locale): void
    {
        $startFrom = 0;
        $exportedIds = [];
        do {
            $batchExportedIds = $this->exportBatch($domainId, $locale, $startFrom);
            $exportedIds = array_merge($exportedIds, $batchExportedIds);
            $startFrom += static::BATCH_SIZE;
        } while (!empty($batchExportedIds));
        $this->removeNotUpdated($domainId, $exportedIds);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param \Symfony\Component\Console\Style\SymfonyStyle $symfonyStyleIo
     */
    public function exportWithOutput(int $domainId, string $locale, SymfonyStyle $symfonyStyleIo): void
    {
        $this->sqlLoggerFacade->temporarilyDisableLogging();

        $startFrom = 0;
        $exportedIds = [];
        $totalCount = $this->productSearchExportRepository->getProductTotalCountForDomainAndLocale($domainId, $locale);

        $progressBar = $this->progressBarFactory->create($symfonyStyleIo, $totalCount);

        do {
            $progressBar->setProgress(min($startFrom, $totalCount));

            $batchExportedIds = $this->exportBatch($domainId, $locale, $startFrom);
            $exportedIds = array_merge($exportedIds, $batchExportedIds);

            $startFrom += static::BATCH_SIZE;

            $this->entityManager->clear();
        } while (!empty($batchExportedIds));

        $progressBar->finish();

        $this->removeNotUpdated($domainId, $exportedIds);

        $this->sqlLoggerFacade->reenableLogging();
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int[] $productIds
     */
    public function exportIds(int $domainId, string $locale, array $productIds): void
    {
        $productsData = $this->productSearchExportRepository->getProductsDataForIds($domainId, $locale, $productIds);
        if (count($productsData) === 0) {
            $this->productElasticsearchRepository->delete($domainId, $productIds);

            return;
        }

        $this->exportProductsData($domainId, $productsData);
        $exportedIds = $this->productElasticsearchConverter->extractIds($productsData);

        $idsToDelete = array_diff($productIds, $exportedIds);

        if ($idsToDelete !== []) {
            $this->productElasticsearchRepository->delete($domainId, $idsToDelete);
        }
    }

    /**
     * @param int $domainId
     * @param array $productsData
     */
    protected function exportProductsData(int $domainId, array $productsData): void
    {
        $data = $this->productElasticsearchConverter->convertExportBulk($productsData);
        $this->productElasticsearchRepository->bulkUpdate($domainId, $data);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int $startFrom
     * @return int[]
     */
    protected function exportBatch(int $domainId, string $locale, int $startFrom): array
    {
        $productsData = $this->productSearchExportRepository->getProductsData($domainId, $locale, $startFrom, static::BATCH_SIZE);
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
