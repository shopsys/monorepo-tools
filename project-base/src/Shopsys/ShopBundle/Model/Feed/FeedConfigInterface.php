<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface;

interface FeedConfigInterface
{
    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getFeedName();

    /**
     * @return string
     */
    public function getTemplateFilepath();

    /**
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository();
}
