<?php

namespace Shopsys\ShopBundle\Model\Feed\HeurekaDelivery;

use Shopsys\ShopBundle\Model\Feed\FeedConfigInterface;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;
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
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
