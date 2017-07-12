<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository;

class HeurekaDeliveryFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository
     */
    private $feedItemRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\HeurekaDelivery\HeurekaDeliveryItemRepository $feedItemRepository
     */
    public function __construct(HeurekaDeliveryItemRepository $feedItemRepository)
    {
        $this->feedItemRepository = $feedItemRepository;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return t('%feedName% - availability', ['%feedName%' => 'Heureka']);
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'heureka_delivery';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysShop/Feed/heurekaDelivery.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig)
    {
        return $items;
    }
}
