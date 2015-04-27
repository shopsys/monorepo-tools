<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;

interface FeedDataSourceInterface {

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \Iterator
	 */
	public function getIterator(DomainConfig $domainConfig);
}
