<?php

namespace SS6\ShopBundle\Component\Sitemap;

use SS6\ShopBundle\Component\Sitemap\SitemapDumperFactory;
use SS6\ShopBundle\Component\Sitemap\SitemapRepository;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;

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

	/**
	 * @var \SS6\ShopBundle\Component\Sitemap\SitemapRepository
	 */
	private $sitemapRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	public function __construct(
		$sitemapsDir,
		Domain $domain,
		SitemapDumperFactory $domainSitemapDumperFactory,
		SitemapRepository $sitemapRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade
	) {
		$this->sitemapsDir = $sitemapsDir;
		$this->domain = $domain;
		$this->domainSitemapDumperFactory = $domainSitemapDumperFactory;
		$this->sitemapRepository = $sitemapRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
	}

	public function generateForAllDomains() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$section = (string)$domainConfig->getId();

			$domainSitemapDumper = $this->domainSitemapDumperFactory->createForDomain($domainConfig->getId());
			$domainSitemapDumper->dump($this->sitemapsDir, $domainConfig->getUrl() . '/', $section);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @return \SS6\ShopBundle\Component\Sitemap\SitemapItem[]
	 */
	public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig) {
		$pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());

		return $this->sitemapRepository->getSitemapItemsForVisibleProducts($domainConfig, $pricingGroup);
	}

}
