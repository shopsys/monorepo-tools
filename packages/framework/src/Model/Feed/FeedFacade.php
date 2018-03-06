<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;
use Shopsys\ProductFeed\FeedConfigInterface;
use Symfony\Component\Filesystem\Filesystem;

class FeedFacade
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 1000;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedXmlWriter
     */
    private $feedXmlWriter;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedConfigFacade
     */
    private $feedConfigFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedGenerationConfig[]
     */
    private $standardFeedGenerationConfigs;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade
     */
    private $productVisibilityFacade;

    public function __construct(
        FeedXmlWriter $feedXmlWriter,
        Domain $domain,
        Filesystem $filesystem,
        FeedConfigFacade $feedConfigFacade,
        FeedGenerationConfigFactory $feedGenerationConfigFactory,
        EntityManagerFacade $entityManagerFacade,
        ProductVisibilityFacade $productVisibilityFacade
    ) {
        $this->feedXmlWriter = $feedXmlWriter;
        $this->domain = $domain;
        $this->filesystem = $filesystem;
        $this->feedConfigFacade = $feedConfigFacade;
        $this->standardFeedGenerationConfigs = $feedGenerationConfigFactory->createAllForStandardFeeds();
        $this->entityManagerFacade = $entityManagerFacade;
        $this->productVisibilityFacade = $productVisibilityFacade;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedGenerationConfig $feedGenerationConfigToContinue
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedGenerationConfig|null
     */
    public function generateStandardFeedsIteratively(FeedGenerationConfig $feedGenerationConfigToContinue)
    {
        foreach ($this->standardFeedGenerationConfigs as $key => $feedGenerationConfig) {
            if ($feedGenerationConfig->isSameFeedAndDomain($feedGenerationConfigToContinue)) {
                $feedConfig = $this->feedConfigFacade->getFeedConfigByName($feedGenerationConfig->getFeedName());
                $domainConfig = $this->domain->getDomainConfigById($feedGenerationConfig->getDomainId());
                $feedItemToContinue = $this->generateFeedBatch(
                    $feedConfig,
                    $domainConfig,
                    $feedGenerationConfigToContinue->getFeedItemId()
                );
                if ($feedItemToContinue !== null) {
                    return new FeedGenerationConfig(
                        $feedConfig->getFeedName(),
                        $domainConfig->getId(),
                        $feedItemToContinue->getId()
                    );
                } else {
                    if (array_key_exists($key + 1, $this->standardFeedGenerationConfigs)) {
                        return $this->standardFeedGenerationConfigs[$key + 1];
                    } else {
                        return null;
                    }
                }
            }
        }

        return null;
    }

    public function generateDeliveryFeeds()
    {
        foreach ($this->feedConfigFacade->getDeliveryFeedConfigs() as $feedConfig) {
            foreach ($this->domain->getAll() as $domainConfig) {
                $this->generateFeed($feedConfig, $domainConfig);
            }
        }
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function generateFeed(
        FeedConfigInterface $feedConfig,
        DomainConfig $domainConfig
    ) {
        $seekItemId = null;
        do {
            $lastFeedItem = $this->generateFeedBatch($feedConfig, $domainConfig, $seekItemId);
            if ($lastFeedItem === null) {
                $seekItemId = null;
            } else {
                $seekItemId = $lastFeedItem->getId();
            }
        } while ($seekItemId !== null);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $seekItemId
     * @return \Shopsys\ProductFeed\FeedItemInterface|null
     */
    private function generateFeedBatch(
        FeedConfigInterface $feedConfig,
        DomainConfig $domainConfig,
        $seekItemId
    ) {
        $filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
        $temporaryFeedFilepath = $filepath . self::TEMPORARY_FILENAME_SUFFIX;

        /*
         * Product is visible, when it has at least one visible category.
         * Hiding a category therefore could cause change of product's visibility but the visibility recalculation is not invoked immediately,
         * so we need to recalculate product's visibility here in order to get consistent data for feed generation.
         */
        $this->productVisibilityFacade->refreshProductsVisibilityForMarked();
        $feedItemRepository = $this->feedConfigFacade->getFeedItemRepositoryByFeedConfig($feedConfig);
        $itemsInBatch = $feedItemRepository->getItems($domainConfig, $seekItemId, self::BATCH_SIZE);

        if ($seekItemId === null) {
            $this->feedXmlWriter->writeBegin(
                $domainConfig,
                $feedConfig->getTemplateFilepath(),
                $temporaryFeedFilepath
            );
        }

        $items = $feedConfig->processItems($itemsInBatch, $domainConfig);
        $this->feedXmlWriter->writeItems(
            $items,
            $domainConfig,
            $feedConfig->getTemplateFilepath(),
            $temporaryFeedFilepath
        );

        $this->entityManagerFacade->clear();

        if (count($itemsInBatch) === self::BATCH_SIZE) {
            return array_pop($itemsInBatch);
        } else {
            $this->feedXmlWriter->writeEnd(
                $domainConfig,
                $feedConfig->getTemplateFilepath(),
                $temporaryFeedFilepath
            );
            $this->filesystem->rename($temporaryFeedFilepath, $filepath, true);

            return null;
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedGenerationConfig
     */
    public function getFirstFeedGenerationConfig()
    {
        return reset($this->standardFeedGenerationConfigs);
    }
}
