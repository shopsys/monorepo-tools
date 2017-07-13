<?php

namespace Shopsys\ProductFeed;

interface FeedItemCustomValuesProviderInterface
{
    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface[] $items
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemCustomValuesInterface[]
     */
    public function getCustomValuesForItems(array $items, DomainConfigInterface $domainConfig);

    /**
     * @param \Shopsys\ProductFeed\FeedItemInterface $item
     * @param \Shopsys\ProductFeed\DomainConfigInterface $domainConfig
     * @return string|null
     */
    public function getHeurekaCategoryNameForItem(FeedItemInterface $item, DomainConfigInterface $domainConfig);
}
