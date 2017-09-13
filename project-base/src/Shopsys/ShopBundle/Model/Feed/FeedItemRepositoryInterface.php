<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ProductFeed\DomainConfigInterface;

interface FeedItemRepositoryInterface
{
    /**
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @param int|null $seekItemId
     * @param int $maxResults
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function getItems(DomainConfigInterface $domainConfig, $seekItemId, $maxResults);
}
