<?php

namespace Shopsys\FrameworkBundle\Model\Feed;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository;
use Shopsys\FrameworkBundle\Model\Feed\Standard\StandardFeedItemRepository;
use Shopsys\ProductFeed\FeedConfigInterface;

class FeedConfigFacade
{
    /**
     * @var string
     */
    private $feedUrlPrefix;

    /**
     * @var string
     */
    private $feedDir;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\FeedConfigRegistry
     */
    private $feedConfigRegistry;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\Standard\StandardFeedItemRepository
     */
    private $standardFeedItemRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository
     */
    private $deliveryFeedItemRepository;

    /**
     * @param string $feedUrlPrefix
     * @param string $feedDir
     * @param \Shopsys\FrameworkBundle\Model\Feed\FeedConfigRegistry $feedConfigRegistry
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Model\Feed\Standard\StandardFeedItemRepository $standardFeedItemRepository
     * @param \Shopsys\FrameworkBundle\Model\Feed\Delivery\DeliveryFeedItemRepository $deliveryFeedItemRepository
     */
    public function __construct(
        $feedUrlPrefix,
        $feedDir,
        FeedConfigRegistry $feedConfigRegistry,
        Setting $setting,
        StandardFeedItemRepository $standardFeedItemRepository,
        DeliveryFeedItemRepository $deliveryFeedItemRepository
    ) {
        $this->feedUrlPrefix = $feedUrlPrefix;
        $this->feedDir = $feedDir;
        $this->feedConfigRegistry = $feedConfigRegistry;
        $this->setting = $setting;
        $this->standardFeedItemRepository = $standardFeedItemRepository;
        $this->deliveryFeedItemRepository = $deliveryFeedItemRepository;
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getStandardFeedConfigs()
    {
        return $this->feedConfigRegistry->getFeedConfigsByType(FeedConfigRegistry::TYPE_STANDARD);
    }

    /**
     * @param string $feedName
     * @return \Shopsys\ProductFeed\FeedConfigInterface
     */
    public function getFeedConfigByName($feedName)
    {
        return $this->feedConfigRegistry->getFeedConfigByName($feedName);
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getDeliveryFeedConfigs()
    {
        return $this->feedConfigRegistry->getFeedConfigsByType(FeedConfigRegistry::TYPE_DELIVERY);
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getAllFeedConfigs()
    {
        return $this->feedConfigRegistry->getAllFeedConfigs();
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedUrl(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        return $domainConfig->getUrl() . $this->feedUrlPrefix . $this->getFeedFilename($feedConfig, $domainConfig);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedFilepath(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        return $this->feedDir . $this->getFeedFilename($feedConfig, $domainConfig);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    private function getFeedFilename(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        $feedHash = $this->setting->get(Setting::FEED_HASH);

        return $feedHash . '_' . $feedConfig->getFeedName() . '_' . $domainConfig->getId() . '.xml';
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @return \Shopsys\FrameworkBundle\Model\Feed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepositoryByFeedConfig(FeedConfigInterface $feedConfig)
    {
        if (in_array($feedConfig, $this->getStandardFeedConfigs(), true)) {
            return $this->standardFeedItemRepository;
        } elseif (in_array($feedConfig, $this->getDeliveryFeedConfigs(), true)) {
            return $this->deliveryFeedItemRepository;
        }

        throw new \Shopsys\FrameworkBundle\Model\Feed\Exception\FeedItemRepositoryForFeedConfigNotFoundException($feedConfig);
    }
}
