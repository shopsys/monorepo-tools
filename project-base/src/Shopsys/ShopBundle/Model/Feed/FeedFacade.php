<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Feed\FeedConfig;
use Shopsys\ShopBundle\Model\Feed\FeedConfigFacade;
use Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig;
use Shopsys\ShopBundle\Model\Feed\FeedGenerationConfigFactory;
use Shopsys\ShopBundle\Model\Feed\FeedXmlWriter;
use Symfony\Component\Filesystem\Filesystem;

class FeedFacade
{
    const TEMPORARY_FILENAME_SUFFIX = '.tmp';
    const BATCH_SIZE = 200;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedXmlWriter
     */
    private $feedXmlWriter;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigFacade
     */
    private $feedConfigFacade;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfigFactory
     */
    private $feedGenerationConfigFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig[]
     */
    private $feedGenerationConfigs;

    /**
     * @var \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade
     */
    private $entityManagerFacade;

    public function __construct(
        FeedXmlWriter $feedXmlWriter,
        Domain $domain,
        Filesystem $filesystem,
        FeedConfigFacade $feedConfigFacade,
        FeedGenerationConfigFactory $feedGenerationConfigFactory,
        EntityManagerFacade $entityManagerFacade
    ) {
        $this->feedXmlWriter = $feedXmlWriter;
        $this->domain = $domain;
        $this->filesystem = $filesystem;
        $this->feedConfigFacade = $feedConfigFacade;
        $this->feedGenerationConfigFactory = $feedGenerationConfigFactory;
        $this->feedGenerationConfigs = $this->feedGenerationConfigFactory->createAll();
        $this->entityManagerFacade = $entityManagerFacade;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig $feedGenerationConfigToContinue
     * @return \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig|null
     */
    public function generateFeedsIteratively(FeedGenerationConfig $feedGenerationConfigToContinue) {
        foreach ($this->feedGenerationConfigs as $key => $feedGenerationConfig) {
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
                        $feedItemToContinue->getItemId()
                    );
                } else {
                    if (array_key_exists($key + 1, $this->feedGenerationConfigs)) {
                        return $this->feedGenerationConfigs[$key + 1];
                    } else {
                        return null;
                    }
                }
            }
        }

        return null;
    }

    public function generateDeliveryFeeds() {
        foreach ($this->feedConfigFacade->getDeliveryFeedConfigs() as $feedConfig) {
            foreach ($this->domain->getAll() as $domainConfig) {
                $this->generateFeed($feedConfig, $domainConfig);
            }
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     */
    public function generateFeed(
        FeedConfig $feedConfig,
        DomainConfig $domainConfig
    ) {
        $seekItemId = null;
        do {
            $lastFeedItem = $this->generateFeedBatch($feedConfig, $domainConfig, $seekItemId);
            if ($lastFeedItem === null) {
                $seekItemId = null;
            } else {
                $seekItemId = $lastFeedItem->getItemId();
            }
        } while ($seekItemId !== null);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $seekItemId
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemInterface|null
     */
    private function generateFeedBatch(
        FeedConfig $feedConfig,
        DomainConfig $domainConfig,
        $seekItemId
    ) {
        $filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
        $temporaryFeedFilepath = $filepath . self::TEMPORARY_FILENAME_SUFFIX;

        $items = $feedConfig->getFeedItemRepository()->getItems($domainConfig, $seekItemId, self::BATCH_SIZE);

        if ($seekItemId === null) {
            $this->feedXmlWriter->writeBegin(
                $domainConfig,
                $feedConfig->getTemplateFilepath(),
                $temporaryFeedFilepath
            );
        }

        $this->feedXmlWriter->writeItems(
            $items,
            $domainConfig,
            $feedConfig->getTemplateFilepath(),
            $temporaryFeedFilepath
        );

        $this->entityManagerFacade->clear();

        if (count($items) === self::BATCH_SIZE) {
            return array_pop($items);
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
     * @return \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig
     */
    public function getFirstFeedGenerationConfig() {
        return reset($this->feedGenerationConfigs);
    }
}
