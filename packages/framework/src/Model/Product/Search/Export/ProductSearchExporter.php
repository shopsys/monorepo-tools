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
     * @param \Shopsys\FrameworkBundle\Component\Console\ProgressBarFactory $progressBarFactory
     * @internal Will be replaced with constructor injection in the next major release
     */
    public function setProgressBarFactory(ProgressBarFactory $progressBarFactory): void
    {
        if ($this->progressBarFactory !== null) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        $this->progressBarFactory = $progressBarFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Doctrine\SqlLoggerFacade $sqlLoggerFacade
     * @internal Will be replaced with constructor injection in the next major release
     */
    public function setSqlLoggerFacade(SqlLoggerFacade $sqlLoggerFacade): void
    {
        if ($this->sqlLoggerFacade !== null) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        $this->sqlLoggerFacade = $sqlLoggerFacade;
    }

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @internal Will be replaced with constructor injection in the next major release
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        if ($this->entityManager !== null) {
            throw new BadMethodCallException(sprintf('Method "%s" has been already called and cannot be called multiple times.', __METHOD__));
        }

        $this->entityManager = $entityManager;
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
        $this->validateInjectedDependencies();

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

    /**
     * @internal Will be removed in the next major release
     */
    protected function validateInjectedDependencies(): void
    {
        if (!$this->progressBarFactory instanceof ProgressBarFactory) {
            throw new BadMethodCallException(sprintf('Method "%s::setProgressBarFactory()" has to be called in "services.yml" definition.', __CLASS__));
        }

        if (!$this->sqlLoggerFacade instanceof SqlLoggerFacade) {
            throw new BadMethodCallException(sprintf('Method "%s::setSqlLoggerFacade()" has to be called in "services.yml" definition.', __CLASS__));
        }

        if (!$this->entityManager instanceof EntityManagerInterface) {
            throw new BadMethodCallException(sprintf('Method "%s::setEntityManager()" has to be called in "services.yml" definition.', __CLASS__));
        }
    }
}
