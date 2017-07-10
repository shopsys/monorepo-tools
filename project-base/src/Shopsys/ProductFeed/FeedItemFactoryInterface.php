<?php

namespace Shopsys\ProductFeed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;

interface FeedItemFactoryInterface
{
    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ProductFeed\FeedItemInterface[]
     */
    public function createItems(array $products, DomainConfig $domainConfig);
}
