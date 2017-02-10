<?php

namespace Shopsys\ShopBundle\Component\Sitemap;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Sitemap\SitemapDumperFactory;
use Shopsys\ShopBundle\Component\Sitemap\SitemapRepository;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class SitemapFacade {

	/**
	 * @var string
	 */
	private $sitemapsDir;

	/**
	 * @var string
	 */
	private $sitemapsUrlPrefix;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Component\Sitemap\SitemapDumperFactory
	 */
	private $domainSitemapDumperFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Sitemap\SitemapRepository
	 */
	private $sitemapRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private $pricingGroupSettingFacade;

	public function __construct(
		$sitemapsDir,
		$sitemapsUrlPrefix,
		Domain $domain,
		SitemapDumperFactory $domainSitemapDumperFactory,
		SitemapRepository $sitemapRepository,
		PricingGroupSettingFacade $pricingGroupSettingFacade
	) {
		$this->sitemapsDir = $sitemapsDir;
		$this->sitemapsUrlPrefix = $sitemapsUrlPrefix;
		$this->domain = $domain;
		$this->domainSitemapDumperFactory = $domainSitemapDumperFactory;
		$this->sitemapRepository = $sitemapRepository;
		$this->pricingGroupSettingFacade = $pricingGroupSettingFacade;
	}

	public function generateForAllDomains() {
		foreach ($this->domain->getAll() as $domainConfig) {
			$section = (string)$domainConfig->getId();

			$domainSitemapDumper = $this->domainSitemapDumperFactory->createForDomain($domainConfig->getId());
			$domainSitemapDumper->dump(
				$this->sitemapsDir,
				$domainConfig->getUrl() . $this->sitemapsUrlPrefix . '/',
				$section
			);
		}
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
	 */
	public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig) {
		$pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());

		return $this->sitemapRepository->getSitemapItemsForVisibleProducts($domainConfig, $pricingGroup);
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
	 */
	public function getSitemapItemsForVisibleCategories(DomainConfig $domainConfig) {
		return $this->sitemapRepository->getSitemapItemsForVisibleCategories($domainConfig);
	}

	/**
	 * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @return \Shopsys\ShopBundle\Component\Sitemap\SitemapItem[]
	 */
	public function getSitemapItemsForArticlesOnDomain(DomainConfig $domainConfig) {
		return $this->sitemapRepository->getSitemapItemsForArticlesOnDomain($domainConfig);
	}

}
