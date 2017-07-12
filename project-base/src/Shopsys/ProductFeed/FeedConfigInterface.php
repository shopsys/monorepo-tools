<?php

namespace Shopsys\ProductFeed;

use Shopsys\ProductFeed\DomainConfigInterface;
use Shopsys\ProductFeed\FeedItemRepositoryInterface;

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
     * @return \Shopsys\ProductFeed\FeedItemRepositoryInterface
     */
    public function getFeedItemRepository();

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function processItems(array $items, DomainConfigInterface $domainConfig);
}
