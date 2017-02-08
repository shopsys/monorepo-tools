<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;

interface FeedItemRepositoryInterface {

	/**
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param int|null $seekItemId
	 * @param int $maxResults
	 * @return \SS6\ShopBundle\Model\Feed\FeedItemInterface[]
	 */
	public function getItems(DomainConfig $domainConfig, $seekItemId, $maxResults);

}
