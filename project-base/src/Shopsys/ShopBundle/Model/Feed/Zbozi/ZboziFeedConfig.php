<?php

namespace Shopsys\ShopBundle\Model\Feed\Zbozi;

use Shopsys\ProductFeed\FeedConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;
use Shopsys\ShopBundle\Model\Feed\Zbozi\ZboziItemRepository;

class ZboziFeedConfig implements FeedConfigInterface
{
    /**
     * @var \Shopsys\ShopBundle\Model\Feed\Zbozi\ZboziItemRepository
     */
    private $feedItemRepository;

    /**
     * @param \Shopsys\ShopBundle\Model\Feed\Zbozi\ZboziItemRepository $feedItemRepository
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
        return '@ShopsysShop/Feed/zbozi.xml.twig';
    }

    /**
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository()
    {
        return $this->feedItemRepository;
    }
}
