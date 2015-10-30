<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;

interface FeedItemFactoryInterface {

	/**
	 * @param \SS6\ShopBundle\Model\Product\Product[] $products
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return array
	 */
	public function createItems(array $products, DomainConfig $domainConfig);

}
