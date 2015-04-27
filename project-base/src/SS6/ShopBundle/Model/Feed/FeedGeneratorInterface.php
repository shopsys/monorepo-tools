<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;

interface FeedGeneratorInterface {

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string $targetFilepath
	 */
	public function generate(DomainConfig $domainConfig, $targetFilepath);
}
