<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;

interface FeedItemFactoryInterface {

	/**
	 * @param \Shopsys\ShopBundle\Model\Product\Product[] $products
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return array
	 */
	public function createItems(array $products, DomainConfig $domainConfig);

}
