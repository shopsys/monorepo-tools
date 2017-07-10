<?php

namespace Shopsys\ProductFeed\ZboziBundle;

use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;

class ZboziFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\ZboziItemRepository
     */
    private $feedItemRepository;

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\ZboziItemRepository $feedItemRepository
     */
    public function __construct(ZboziItemRepository $feedItemRepository)
    {
        $this->feedItemRepository = $feedItemRepository;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return 'Zboží.cz';
    }

    /**
     * @return string
     */
    public function getFeedName()
    {
        return 'zbozi';
    }

    /**
     * @return string
     */
    public function getTemplateFilepath()
    {
        return '@ShopsysProductFeedZbozi/feed.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
