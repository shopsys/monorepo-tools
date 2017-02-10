<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\Model\Feed\FeedConfig;
use Shopsys\ShopBundle\Model\Feed\FeedConfigRepository;

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
     * @param string $feedUrlPrefix
     * @param string $feedDir
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfigRepository $feedConfigRepository
     * @param \Shopsys\ShopBundle\Component\Setting\Setting $setting
     */
    public function __construct(
        $feedUrlPrefix,
        $feedDir,
        FeedConfigRepository $feedConfigRepository,
        Setting $setting
    ) {
        $this->feedUrlPrefix = $feedUrlPrefix;
        $this->feedDir = $feedDir;
        $this->feedConfigRepository = $feedConfigRepository;
        $this->setting = $setting;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
     */
    public function getFeedConfigs() {
        return $this->feedConfigRepository->getFeedConfigs();
    }

    /**
     * @param string $feedName
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig
     */
    public function getFeedConfigByName($feedName) {
        return $this->feedConfigRepository->getFeedConfigByName($feedName);
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
     */
    public function getDeliveryFeedConfigs() {
        return $this->feedConfigRepository->getDeliveryFeedConfigs();
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfig[]
     */
    public function getAllFeedConfigs() {
        return $this->feedConfigRepository->getAllFeedConfigs();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedUrl(FeedConfig $feedConfig, DomainConfig $domainConfig) {
        $feedHash = $this->setting->get(Setting::FEED_HASH);
        return $domainConfig->getUrl() . $this->feedUrlPrefix . $feedConfig->getFeedFilename($domainConfig, $feedHash);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfig $feedConfig
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string
     */
    public function getFeedFilepath(FeedConfig $feedConfig, DomainConfig $domainConfig) {
        $feedHash = $this->setting->get(Setting::FEED_HASH);
        return $this->feedDir . $feedConfig->getFeedFilename($domainConfig, $feedHash);
    }
}
