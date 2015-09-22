<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedDataSourceInterface;
use SS6\ShopBundle\Model\Feed\FeedGenerator;
use Symfony\Component\Filesystem\Filesystem;

class FeedFacade {

	const TEMPORARY_FILENAME_SULFIX = '.tmp';

	/**
	 * @var string
	 */
	private $feedsPath;

	/**
	 * @var FeedDataSourceInterface
	 */
	private $heurekaFeedDataSource;

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
	 * @var \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface
	 */
	private $heurekaDeliveryFeedDataSource;

	public function __construct(
		$feedsPath,
		FeedDataSourceInterface $heurekaFeedDataSource,
		FeedDataSourceInterface $heurekaDeliveryFeedDataSource,
		FeedGenerator $feedGenerator,
		Domain $domain,
		Filesystem $filesystem
	) {
		$this->feedsPath = $feedsPath;
		$this->heurekaFeedDataSource = $heurekaFeedDataSource;
		$this->feedGenerator = $feedGenerator;
		$this->domain = $domain;
		$this->filesystem = $filesystem;
		$this->heurekaDeliveryFeedDataSource = $heurekaDeliveryFeedDataSource;
	}

	public function generateAllFeeds() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$feedFilename = 'heureka_' . $domainConfig->getId() . '.xml';
			$this->generateFeed(
				$feedFilename,
				$domainConfig,
				$this->heurekaFeedDataSource,
				'@SS6Shop/Feed/heureka.xml.twig'
			);
		}
	}

	public function generateAllDeliveryFeeds() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$feedFilename = 'heureka_delivery_' . $domainConfig->getId() . '.xml';
			$this->generateFeed(
				$feedFilename,
				$domainConfig,
				$this->heurekaDeliveryFeedDataSource,
				'@SS6Shop/Feed/heurekaDelivery.xml.twig'
			);
		}
	}

	/**
	 * @param string $feedFilename
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param \SS6\ShopBundle\Model\Feed\FeedDataSourceInterface $feedDataSource
	 * @param string $templateFilepath
	 */
	private function generateFeed(
		$feedFilename,
		DomainConfig $domainConfig,
		FeedDataSourceInterface $feedDataSource,
		$templateFilepath
	) {
		$temporaryFeedFilepath = $this->feedsPath . '/' . $feedFilename . self::TEMPORARY_FILENAME_SULFIX;
		$this->feedGenerator->generate(
			$feedDataSource,
			$domainConfig,
			$templateFilepath,
			$temporaryFeedFilepath
		);
		$this->filesystem->rename($temporaryFeedFilepath, $this->feedsPath . '/' . $feedFilename, true);
	}
}
