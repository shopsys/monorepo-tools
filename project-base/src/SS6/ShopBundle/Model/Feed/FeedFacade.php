<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Model\Feed\FeedGenerator;
use Symfony\Component\Filesystem\Filesystem;

class FeedFacade {

	const TEMPORARY_FILENAME_SULFIX = '.tmp';

	/**
	 * @var string
	 */
	private $feedsPath;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedGenerator
	 */
	private $feedGenerator;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedConfigFacade
	 */
	private $feedConfigFacade;

	public function __construct(
		$feedsPath,
		FeedGenerator $feedGenerator,
		Domain $domain,
		Filesystem $filesystem,
		FeedConfigFacade $feedConfigFacade
	) {
		$this->feedsPath = $feedsPath;
		$this->feedGenerator = $feedGenerator;
		$this->domain = $domain;
		$this->filesystem = $filesystem;
		$this->feedConfigFacade = $feedConfigFacade;
	}

	public function generateAllFeeds() {
		foreach ($this->feedConfigFacade->getAllFeedConfigs() as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->generateFeed($feedConfig, $domainConfig);
			}
		}
	}

	public function generateAllDeliveryFeeds() {
		foreach ($this->feedConfigFacade->getAllDeliveryFeedConfigs() as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->generateFeed($feedConfig, $domainConfig);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 */
	private function generateFeed(
		FeedConfig $feedConfig,
		DomainConfig $domainConfig
	) {
		$filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
		$temporaryFeedFilepath = $filepath . self::TEMPORARY_FILENAME_SULFIX;

		$this->feedGenerator->generate(
			$feedConfig->getFeedDataSource(),
			$domainConfig,
			$feedConfig->getTemplateFilepath(),
			$temporaryFeedFilepath
		);
		$this->filesystem->rename($temporaryFeedFilepath, $filepath, true);
	}
}
