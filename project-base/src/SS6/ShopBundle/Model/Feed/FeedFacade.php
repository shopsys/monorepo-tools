<?php

namespace SS6\ShopBundle\Model\Feed;

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

	public function __construct(
		$feedsPath,
		FeedDataSourceInterface $heurekaFeedDataSource,
		FeedGenerator $feedGenerator,
		Domain $domain,
		Filesystem $filesystem
	) {
		$this->feedsPath = $feedsPath;
		$this->heurekaFeedDataSource = $heurekaFeedDataSource;
		$this->feedGenerator = $feedGenerator;
		$this->domain = $domain;
		$this->filesystem = $filesystem;
	}

	public function generateAllFeeds() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$feedFilename = 'heureka_' . $domainConfig->getId() . '.xml';

			$temporaryFeedFilepath = $this->feedsPath . '/' . $feedFilename . self::TEMPORARY_FILENAME_SULFIX;
			$this->feedGenerator->generate(
				$this->heurekaFeedDataSource,
				$domainConfig,
				'@SS6Shop/Feed/heureka.xml.twig',
				$temporaryFeedFilepath
			);
			$this->filesystem->rename($temporaryFeedFilepath, $this->feedsPath . '/' . $feedFilename, true);
		}
	}
}
