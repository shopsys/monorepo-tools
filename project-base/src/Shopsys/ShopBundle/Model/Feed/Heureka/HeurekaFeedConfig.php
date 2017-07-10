<?php

namespace Shopsys\ShopBundle\Model\Feed\Heureka;

use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItemRepository;

class HeurekaFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItemRepository
     */
    private $feedItemRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\Heureka\HeurekaItemRepository $feedItemRepository
     */
    public function __construct(HeurekaItemRepository $feedItemRepository)
    {
        $this->feedItemRepository = $feedItemRepository;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Heureka';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'heureka';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysShop/Feed/heureka.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
