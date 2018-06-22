<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

class FeedFacade
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedRegistry
     */
    protected $feedRegistry;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    protected $productVisibilityFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedExportFactory
     */
    protected $feedExportFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedPathProvider
     */
    protected $feedPathProvider;

    public function __construct(
        FeedRegistry $feedRegistry,
        ProductVisibilityFacade $productVisibilityFacade,
        FeedExportFactory $feedExportFactory,
        FeedPathProvider $feedPathProvider
    ) {
        $this->feedRegistry = $feedRegistry;
        $this->productVisibilityFacade = $productVisibilityFacade;
        $this->feedExportFactory = $feedExportFactory;
        $this->feedPathProvider = $feedPathProvider;
    }

    /**
     * @param string $feedName
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function generateFeed(string $feedName, DomainConfig $domainConfig): void
    {
        $feedExport = $this->createFeedExport($feedName, $domainConfig);

        while (!$feedExport->isFinished()) {
            $feedExport->generateBatch();
        }
    }

    /**
     * @param string $feedName
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $lastSeekId
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedExport
     */
    public function createFeedExport(string $feedName, DomainConfig $domainConfig, int $lastSeekId = null): FeedExport
    {
        /*
         * Product is visible, when it has at least one visible category.
         * Hiding a category therefore could cause change of product's visibility but the visibility recalculation is not invoked immediately,
         * so we need to recalculate product's visibility here in order to get consistent data for feed generation.
         */
        $this->productVisibilityFacade->refreshProductsVisibilityForMarked();

        $feed = $this->feedRegistry->getFeedByName($feedName);

        return $this->feedExportFactory->create($feed, $domainConfig, $lastSeekId);
    }

    /**
     * @param string|null $feedType
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface[]
     */
    public function getFeedsInfo(string $feedType = null): array
    {
        $feeds = $feedType === null ? $this->feedRegistry->getAllFeeds() : $this->feedRegistry->getFeeds($feedType);

        $feedsInfo = [];
        foreach ($feeds as $feed) {
            $feedsInfo[] = $feed->getInfo();
        }

        return $feedsInfo;
    }

    /**
     * @param string|null $feedType
     * @return string[]
     */
    public function getFeedNames(string $feedType = null): array
    {
        $feedNames = [];
        foreach ($this->getFeedsInfo($feedType) as $feedInfo) {
            $feedNames[] = $feedInfo->getName();
        }

        return $feedNames;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feedInfo
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedUrl(FeedInfoInterface $feedInfo, DomainConfig $domainConfig): string
    {
        return $this->feedPathProvider->getFeedUrl($feedInfo, $domainConfig);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedInfoInterface $feedInfo
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedFilepath(FeedInfoInterface $feedInfo, DomainConfig $domainConfig): string
    {
        return $this->feedPathProvider->getFeedFilepath($feedInfo, $domainConfig);
    }
}
