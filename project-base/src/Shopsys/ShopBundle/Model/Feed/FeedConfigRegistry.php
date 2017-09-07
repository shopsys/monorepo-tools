<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ProductFeed\FeedConfigInterface;

class FeedConfigRegistry
{
    /**
     * @var \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    private $feedConfigs;

    /**
     * @var \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    private $deliveryFeedConfigs;

    public function __construct()
    {
        $this->feedConfigs = [];
        $this->deliveryFeedConfigs = [];
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     */
    public function registerFeedConfig(FeedConfigInterface $feedConfig)
    {
        $this->feedConfigs[] = $feedConfig;
    }

    /**
     * @param \Shopsys\ProductFeed\FeedConfigInterface $feedConfig
     */
    public function registerDeliveryFeedConfig(FeedConfigInterface $feedConfig)
    {
        $this->deliveryFeedConfigs[] = $feedConfig;
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getFeedConfigs()
    {
        return $this->feedConfigs;
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getDeliveryFeedConfigs()
    {
        return $this->deliveryFeedConfigs;
    }

    /**
     * @param string $feedName
     * @return \Shopsys\ProductFeed\FeedConfigInterface
     */
    public function getFeedConfigByName($feedName)
    {
        foreach ($this->getAllFeedConfigs() as $feedConfig) {
            if ($feedConfig->getFeedName() === $feedName) {
                return $feedConfig;
            }
        }

        $message = 'Feed config with name "' . $feedName . ' not found.';
        throw new \Shopsys\ShopBundle\Model\Feed\Exception\FeedConfigNotFoundException($message);
    }

    /**
     * @return \Shopsys\ProductFeed\FeedConfigInterface[]
     */
    public function getAllFeedConfigs()
    {
        return array_merge($this->getFeedConfigs(), $this->getDeliveryFeedConfigs());
    }
}
