<?php

namespace Shopsys\ProductFeed\HeurekaBundle;

use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;

class HeurekaFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\HeurekaItemRepository
     */
    private $feedItemRepository;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\HeurekaItemRepository $feedItemRepository
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
        return '@ShopsysProductFeedHeureka/feed.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
