<?php

namespace SS6\ShopBundle\Component\Sitemap;

use SS6\ShopBundle\Component\Sitemap\SitemapDumperFactory;
use SS6\ShopBundle\Model\Domain\Domain;

class SitemapFacade {

	/**
	 * @var string
	 */
	private $sitemapsDir;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Sitemap\SitemapDumperFactory
	 */
	private $domainSitemapDumperFactory;

	public function __construct($sitemapsDir, Domain $domain, SitemapDumperFactory $domainSitemapDumperFactory) {
		$this->sitemapsDir = $sitemapsDir;
		$this->domain = $domain;
		$this->domainSitemapDumperFactory = $domainSitemapDumperFactory;
	}

	public function generateForAllDomains() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$domainSitemapDumper = $this->domainSitemapDumperFactory->createForDomain($domainConfig->getId());
			$domainSitemapDumper->dump($this->sitemapsDir, $domainConfig->getUrl() . '/', $domainConfig->getId());
		}
	}

}
