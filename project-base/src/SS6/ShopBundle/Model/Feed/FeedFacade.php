<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedConfig;
use SS6\ShopBundle\Model\Feed\FeedConfigFacade;
use SS6\ShopBundle\Model\Feed\FeedGenerationConfig;
use SS6\ShopBundle\Model\Feed\FeedGenerationConfigFactory;
use SS6\ShopBundle\Model\Feed\FeedGenerator;
use Symfony\Component\Filesystem\Filesystem;

class FeedFacade {

	const TEMPORARY_FILENAME_SUFFIX = '.tmp';

	/**
	 * @var string
	 */
	private $feedsPath;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
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

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedGenerationConfigFactory
	 */
	private $feedGenerationConfigFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\FeedGenerationConfig[]
	 */
	private $feedGenerationConfigs;

	public function __construct(
		$feedsPath,
		FeedGenerator $feedGenerator,
		Domain $domain,
		Filesystem $filesystem,
		FeedConfigFacade $feedConfigFacade,
		FeedGenerationConfigFactory $feedGenerationConfigFactory
	) {
		$this->feedsPath = $feedsPath;
		$this->feedGenerator = $feedGenerator;
		$this->domain = $domain;
		$this->filesystem = $filesystem;
		$this->feedConfigFacade = $feedConfigFacade;
		$this->feedGenerationConfigFactory = $feedGenerationConfigFactory;
		$this->feedGenerationConfigs = $this->feedGenerationConfigFactory->createAll();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedGenerationConfig $feedGenerationConfigToContinue
	 * @return \SS6\ShopBundle\Model\Feed\FeedGenerationConfig|null
	 */
	public function generateFeedsIteratively(FeedGenerationConfig $feedGenerationConfigToContinue) {
		foreach ($this->feedGenerationConfigs as $key => $feedGenerationConfig) {
			if ($feedGenerationConfig->isSameFeedAndDomain($feedGenerationConfigToContinue)) {
				$feedConfig = $this->feedConfigFacade->getFeedConfigByName($feedGenerationConfig->getFeedName());
				$domainConfig = $this->domain->getDomainConfigById($feedGenerationConfig->getDomainId());
				$feedItemToContinue = $this->generateFeedBatch(
					$feedConfig,
					$domainConfig,
					$feedGenerationConfigToContinue->getFeedItemId()
				);
				if ($feedItemToContinue !== null) {
					return new FeedGenerationConfig(
						$feedConfig->getFeedName(),
						$domainConfig->getId(),
						$feedItemToContinue->getItemId()
					);
				} else {
					if (array_key_exists($key + 1, $this->feedGenerationConfigs)) {
						return $this->feedGenerationConfigs[$key + 1];
					} else {
						return null;
					}
				}
			}
		}

		return null;
	}

	public function generateDeliveryFeeds() {
		foreach ($this->feedConfigFacade->getDeliveryFeedConfigs() as $feedConfig) {
			foreach ($this->domain->getAll() as $domainConfig) {
				$this->generateFeed($feedConfig, $domainConfig);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 */
	public function generateFeed(
		FeedConfig $feedConfig,
		DomainConfig $domainConfig
	) {
		$seekItemId = null;
		do {
			$lastFeedItem = $this->generateFeedBatch($feedConfig, $domainConfig, $seekItemId);
			if ($lastFeedItem === null) {
				$seekItemId = null;
			} else {
				$seekItemId = $lastFeedItem->getItemId();
			}
		} while ($seekItemId !== null);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Feed\FeedConfig $feedConfig
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param int|null $feedItemIdToContinue
	 * @return \SS6\ShopBundle\Model\Feed\FeedItemInterface|null
	 */
	private function generateFeedBatch(
		FeedConfig $feedConfig,
		DomainConfig $domainConfig,
		$feedItemIdToContinue
	) {
		$filepath = $this->feedConfigFacade->getFeedFilepath($feedConfig, $domainConfig);
		$temporaryFeedFilepath = $filepath . self::TEMPORARY_FILENAME_SUFFIX;

		$feedItemToContinue = $this->feedGenerator->generateIteratively(
			$feedConfig->getFeedItemIteratorFactory(),
			$domainConfig,
			$feedConfig->getTemplateFilepath(),
			$temporaryFeedFilepath,
			$feedItemIdToContinue
		);
		if ($feedItemToContinue === null) {
			$this->filesystem->rename($temporaryFeedFilepath, $filepath, true);
		}

		return $feedItemToContinue;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Feed\FeedGenerationConfig
	 */
	public function getFirstFeedGenerationConfig() {
		return reset($this->feedGenerationConfigs);
	}
}
