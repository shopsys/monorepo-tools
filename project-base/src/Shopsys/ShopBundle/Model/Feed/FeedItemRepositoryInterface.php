<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;

interface FeedItemRepositoryInterface {

    /**
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @param int|null $seekItemId
     * @param int $maxResults
     * @return \Shopsys\ShopBundle\Model\Feed\FeedItemInterface[]
     */
    public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults);

}
