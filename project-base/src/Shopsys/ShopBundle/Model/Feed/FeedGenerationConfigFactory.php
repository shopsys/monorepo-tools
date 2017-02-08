<?php

namespace Shopsys\ShopBundle\Model\Feed;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Feed\FeedConfigFacade;
use Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig;

class FeedGenerationConfigFactory {

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\FeedConfigFacade
	 */
	private $feedConfigFacade;

	public function __construct(Domain $domain, FeedConfigFacade $feedConfigFacade) {
		$this->domain = $domain;
		$this->feedConfigFacade = $feedConfigFacade;
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Feed\FeedGenerationConfig[]
	 */
	public function createAll() {
		$feedGenerationConfigs = [];
		foreach ($this->feedConfigFacade->getFeedConfigs() as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$feedGenerationConfigs[] = new FeedGenerationConfig(
					$feedConfig->getFeedName(),
					$domainConfig->getId()
				);
			}
		}

		return $feedGenerationConfigs;
	}

}
