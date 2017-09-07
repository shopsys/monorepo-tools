<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository;
use Shopsys\ShopBundle\Model\Feed\FeedConfigRepository;
use Shopsys\ShopBundle\Model\Feed\Standard\StandardFeedItemRepository;

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
     * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigRepository
     */
    private $feedConfigRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Setting\Setting
     */
    private $setting;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Standard\StandardFeedItemRepository
     */
    private $standardFeedItemRepository;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository
     */
    private $deliveryFeedItemRepository;

    /**
     * @param string $feedUrlPrefix
     * @param string $feedDir
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfigRepository $feedConfigRepository
     * @param \Shopsys\ShopBundle\Component\Setting\Setting $setting
     * @param \Shopsys\ShopBundle\Model\Feed\Standard\StandardFeedItemRepository $standardFeedItemRepository
     * @param \Shopsys\ShopBundle\Model\Feed\Delivery\DeliveryFeedItemRepository $deliveryFeedItemRepository
     */
    public function __construct(
        $feedUrlPrefix,
        $feedDir,
        FeedConfigRepository $feedConfigRepository,
        Setting $setting,
        StandardFeedItemRepository $standardFeedItemRepository,
        DeliveryFeedItemRepository $deliveryFeedItemRepository
    ) {
        $this->feedUrlPrefix = $feedUrlPrefix;
        $this->feedDir = $feedDir;
        $this->feedConfigRepository = $feedConfigRepository;
        $this->setting = $setting;
        $this->standardFeedItemRepository = $standardFeedItemRepository;
        $this->deliveryFeedItemRepository = $deliveryFeedItemRepository;
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getFeedConfigs()
    {
        return $this->feedConfigRepository->getFeedConfigs();
    }

    /**
     * @param string $feedName
     * @return \Shopsys\ProductFeed\FeedConfigInterface
     */
    public function getFeedConfigByName($feedName)
    {
        return $this->feedConfigRepository->getFeedConfigByName($feedName);
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getDeliveryFeedConfigs()
    {
        return $this->feedConfigRepository->getDeliveryFeedConfigs();
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getAllFeedConfigs()
    {
        return $this->feedConfigRepository->getAllFeedConfigs();
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedUrl(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        return $domainConfig->getUrl() . $this->feedUrlPrefix . $this->getFeedFilename($feedConfig, $domainConfig);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedFilepath(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        return $this->feedDir . $this->getFeedFilename($feedConfig, $domainConfig);
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    private function getFeedFilename(FeedConfigInterface $feedConfig, DomainConfig $domainConfig)
    {
        $feedHash = $this->setting->get(Setting::FEED_HASH);

        return $feedHash . '_' . $feedConfig->getFeedName() . '_' . $domainConfig->getId() . '.xml';
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepositoryByFeedConfig(FeedConfigInterface $feedConfig)
    {
        if (in_array($feedConfig, $this->getFeedConfigs(), true)) {
            return $this->standardFeedItemRepository;
        } elseif (in_array($feedConfig, $this->getDeliveryFeedConfigs(), true)) {
            return $this->deliveryFeedItemRepository;
        }

        throw new \Shopsys\ShopBundle\Model\Feed\Exception\FeedItemRepositoryForFeedConfigNotFoundException($feedConfig);
    }
}
