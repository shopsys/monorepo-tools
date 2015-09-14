<?php

namespace SS6\ShopBundle\Component\Sitemap;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\AbstractGenerator;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use SS6\ShopBundle\Component\Sitemap\SitemapFacade;
use SS6\ShopBundle\Model\Domain\Config\DomainConfig;
use SS6\ShopBundle\Model\Domain\Domain;

class SitemapListener implements SitemapListenerInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Sitemap\SitemapFacade
	 */
	private $sitemapFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService
	 */
	private $friendlyUrlService;

	public function __construct(
		SitemapFacade $sitemapFacade,
		Domain $domain,
		FriendlyUrlService $friendlyUrlService
	) {
		$this->sitemapFacade = $sitemapFacade;
		$this->domain = $domain;
		$this->friendlyUrlService = $friendlyUrlService;
	}

	/**
	 * @param \Presta\SitemapBundle\Event\SitemapPopulateEvent $event
	 */
	public function populateSitemap(SitemapPopulateEvent $event) {
		$section = $event->getSection();
		$domainId = (int)$section;

		$generator = $event->getGenerator();
		$domainConfig = $this->domain->getDomainConfigById($domainId);

		$productSitemapItems = $this->sitemapFacade->getSitemapItemsForVisibleProducts($domainConfig);
		$this->addUrlsBySitemapItems($productSitemapItems, $generator, $domainConfig, $section);

		$categorySitemapItems = $this->sitemapFacade->getSitemapItemsForVisibleCategories($domainConfig);
		$this->addUrlsBySitemapItems($categorySitemapItems, $generator, $domainConfig, $section);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Sitemap\SitemapItem[] $sitemapItems
	 * @param \Presta\SitemapBundle\Service\AbstractGenerator $generator
	 * @param \SS6\ShopBundle\Model\Domain\Config\DomainConfig $domainConfig
	 * @param string $section
	 */
	private function addUrlsBySitemapItems(
		array $sitemapItems,
		AbstractGenerator $generator,
		DomainConfig $domainConfig,
		$section
	) {
		foreach ($sitemapItems as $sitemapItem) {
			$absoluteUrl = $this->friendlyUrlService->getAbsoluteUrlByDomainConfigAndSlug($domainConfig, $sitemapItem->slug);
			$urlConcrete = new UrlConcrete($absoluteUrl);
			$generator->addUrl($urlConcrete, $section);
		}
	}

}
