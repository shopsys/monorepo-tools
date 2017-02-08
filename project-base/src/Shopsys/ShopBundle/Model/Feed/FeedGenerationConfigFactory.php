<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Model\Feed\FeedGenerationConfig;

class FeedGenerationConfigFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedConfigFacade
	 */
	private $feedConfigFacade;

	public function __construct(Domain $domain, FeedConfigFacade $feedConfigFacade) {
		$this->domain = $domain;
		$this->feedConfigFacade = $feedConfigFacade;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedGenerationConfig[]
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
