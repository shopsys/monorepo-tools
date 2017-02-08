<?php

namespace SS6\ShopBundle\Component\Sitemap;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\AbstractGenerator;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use SS6\ShopBundle\Component\Domain\Config\DomainConfig;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Router\DomainRouterFactory;
use SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService;
use SS6\ShopBundle\Component\Sitemap\SitemapFacade;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SitemapListener implements SitemapListenerInterface {

	const PRIORITY_HOMEPAGE = 1;
	const PRIORITY_CATEGORIES = 0.8;
	const PRIORITY_PRODUCTS = 0.7;
	const PRIORITY_ARTICLES = 0.5;

	/**
	 * @var \SS6\ShopBundle\Component\Sitemap\SitemapFacade
	 */
	private $sitemapFacade;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Router\FriendlyUrl\FriendlyUrlService
	 */
	private $friendlyUrlService;

	/**
	 * @var \SS6\ShopBundle\Component\Router\DomainRouterFactory
	 */
	private $domainRouterFactory;

	public function __construct(
		SitemapFacade $sitemapFacade,
		Domain $domain,
		FriendlyUrlService $friendlyUrlService,
		DomainRouterFactory $domainRouterFactory
	) {
		$this->sitemapFacade = $sitemapFacade;
		$this->domain = $domain;
		$this->friendlyUrlService = $friendlyUrlService;
		$this->domainRouterFactory = $domainRouterFactory;
	}

	/**
	 * @param \Presta\SitemapBundle\Event\SitemapPopulateEvent $event
	 */
	public function populateSitemap(SitemapPopulateEvent $event) {
		$section = $event->getSection();
		$domainId = (int)$section;

		$generator = $event->getGenerator();
		$domainConfig = $this->domain->getDomainConfigById($domainId);

		$this->addHomepageUrl($generator, $domainConfig, $section, self::PRIORITY_HOMEPAGE);

		$productSitemapItems = $this->sitemapFacade->getSitemapItemsForVisibleProducts($domainConfig);
		$this->addUrlsBySitemapItems($productSitemapItems, $generator, $domainConfig, $section, self::PRIORITY_PRODUCTS);

		$categorySitemapItems = $this->sitemapFacade->getSitemapItemsForVisibleCategories($domainConfig);
		$this->addUrlsBySitemapItems($categorySitemapItems, $generator, $domainConfig, $section, self::PRIORITY_CATEGORIES);

		$articleSitemapItems = $this->sitemapFacade->getSitemapItemsForArticlesOnDomain($domainConfig);
		$this->addUrlsBySitemapItems($articleSitemapItems, $generator, $domainConfig, $section, self::PRIORITY_ARTICLES);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Sitemap\SitemapItem[] $sitemapItems
	 * @param \Presta\SitemapBundle\Service\AbstractGenerator $generator
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $section
	 * @param int $elementPriority
	 */
	private function addUrlsBySitemapItems(
		array $sitemapItems,
		AbstractGenerator $generator,
		DomainConfig $domainConfig,
		$section,
		$elementPriority
	) {
		foreach ($sitemapItems as $sitemapItem) {
			$absoluteUrl = $this->friendlyUrlService->getAbsoluteUrlByDomainConfigAndSlug($domainConfig, $sitemapItem->slug);
			$urlConcrete = new UrlConcrete($absoluteUrl, null, null, $elementPriority);
			$generator->addUrl($urlConcrete, $section);
		}
	}

	/**
	 * @param \Presta\SitemapBundle\Service\AbstractGenerator $generator
	 * @param \SS6\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
	 * @param string $section
	 * @param int $elementPriority
	 */
	private function addHomepageUrl(
		AbstractGenerator $generator,
		DomainConfig $domainConfig,
		$section,
		$elementPriority
	) {
		$domainRouter = $this->domainRouterFactory->getRouter($domainConfig->getId());
		$absoluteUrl = $domainRouter->generate('front_homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
		$urlConcrete = new UrlConcrete($absoluteUrl, null, null, $elementPriority);
		$generator->addUrl($urlConcrete, $section);
	}

}
