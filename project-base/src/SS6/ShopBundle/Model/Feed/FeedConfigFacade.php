<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedConfigRepository;

class FeedConfigFacade {

	/**
	 * @var string
	 */
	private $feedUrlPrefix;

	/**
	 * @var string
	 */
	private $feedDir;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedConfigRepository
	 */
	private $feedConfigRepository;

	/**
	 * @param string $feedUrlPrefix
	 * @param string $feedDir
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfigRepository $feedConfigRepository
	 */
	public function __construct(
		$feedUrlPrefix,
		$feedDir,
		FeedConfigRepository $feedConfigRepository
	) {
		$this->feedUrlPrefix = $feedUrlPrefix;
		$this->feedDir = $feedDir;
		$this->feedConfigRepository = $feedConfigRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedConfig[]
	 */
	public function getAllFeedConfigs() {
		return $this->feedConfigRepository->getAllFeedConfigs();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return string
	 */
	public function getFeedUrl(FeedConfig $feedConfig, DomainConfig $domainConfig) {
		return $domainConfig->getUrl() . $this->feedUrlPrefix . $feedConfig->getFeedFilename($domainConfig);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return string
	 */
	public function getFeedFilepath(FeedConfig $feedConfig, DomainConfig $domainConfig) {
		return $this->feedDir . '/' . $feedConfig->getFeedFilename($domainConfig);
	}

}
