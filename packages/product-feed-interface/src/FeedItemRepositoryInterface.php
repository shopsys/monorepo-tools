<?php

namespace Shopsys\ProductFeed;

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
