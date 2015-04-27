<?php

namespace SS6\ShopBundle\Model\Feed;

use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Feed\FeedGeneratorInterface;
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
	 * @var \SS6\ShopBundle\Model\Feed\FeedGeneratorInterface
	 */
	private $heurekaGenerator;

	public function __construct(
		$feedsPath,
		FeedGeneratorInterface $heurekaGenerator,
		Domain $domain,
		Filesystem $filesystem
	) {
		$this->feedsPath = $feedsPath;
		$this->heurekaGenerator = $heurekaGenerator;
		$this->domain = $domain;
		$this->filesystem = $filesystem;
	}

	public function generateAllFeeds() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$feedFilename = 'heureka_' . $domainConfig->getId() . '.xml';

			$temporaryFeedFilepath = $this->feedsPath . '/' . $feedFilename . self::TEMPORARY_FILENAME_SULFIX;
			$this->heurekaGenerator->generate($domainConfig, $temporaryFeedFilepath);
			$this->filesystem->rename($temporaryFeedFilepath, $this->feedsPath . '/' . $feedFilename, true);
		}
	}
}
