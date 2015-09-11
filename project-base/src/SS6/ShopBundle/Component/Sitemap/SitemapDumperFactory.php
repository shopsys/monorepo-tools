<?php

namespace SS6\ShopBundle\Component\Sitemap;

use Presta\SitemapBundle\Service\Dumper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;

class SitemapDumperFactory {

	const MAX_ITEMS_IN_FILE = 50000;

	/**
	 * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
	 */
	private $eventDispatcher;

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;

	public function __construct(EventDispatcherInterface $eventDispatcher, Filesystem $filesystem) {
		$this->eventDispatcher = $eventDispatcher;
		$this->filesystem = $filesystem;
	}

	/**
	 * @param int $domainId
	 * @return \Presta\SitemapBundle\Service\Dumper
	 */
	public function createForDomain($domainId) {
		return new Dumper(
			$this->eventDispatcher,
			$this->filesystem,
			'domain_' . $domainId . '_sitemap',
			self::MAX_ITEMS_IN_FILE
		);
	}
}
