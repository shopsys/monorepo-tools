<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Model\Feed\FeedConfigInterface;

class FeedConfigRepository
{
    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface[]
     */
    private $feedConfigs;

    /**
     * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface[]
     */
    private $deliveryFeedConfigs;

    public function __construct()
    {
        $this->feedConfigs = [];
        $this->deliveryFeedConfigs = [];
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface $feedConfig
     */
    public function registerFeedConfig(FeedConfigInterface $feedConfig)
    {
        $this->feedConfigs[] = $feedConfig;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface $feedConfig
     */
    public function registerDeliveryFeedConfig(FeedConfigInterface $feedConfig)
    {
        $this->deliveryFeedConfigs[] = $feedConfig;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface[]
     */
    public function getFeedConfigs()
    {
        return $this->feedConfigs;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface[]
     */
    public function getDeliveryFeedConfigs()
    {
        return $this->deliveryFeedConfigs;
    }

    /**
     * @param string $feedName
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface
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
     * @return \Shopsys\ShopBundle\Model\Feed\FeedConfigInterface[]
     */
    public function getAllFeedConfigs()
    {
        return array_merge($this->getFeedConfigs(), $this->getDeliveryFeedConfigs());
    }
}
