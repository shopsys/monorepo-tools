<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Component\Controller\FrontBaseController;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Component\Sitemap\SitemapService;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends FrontBaseController {

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Component\Sitemap\SitemapService
	 */
	private $sitemapService;

	public function __construct(
		Domain $domain,
		SitemapService $sitemapService
	) {
		$this->domain = $domain;
		$this->sitemapService = $sitemapService;
	}

	public function indexAction() {
		$sitemapsUrlPrefix = $this->get('service_container')->getParameter('ss6.sitemaps_url_prefix');
		$sitemapFilePrefix = $this->sitemapService->getSitemapFilePrefixForDomain($this->domain->getId());

		$sitemapUrl = $this->domain->getUrl() . $sitemapsUrlPrefix . '/' . $sitemapFilePrefix . '.xml';

		$response = new Response();
		$response->headers->set('Content-Type', 'text/plain');

		return $this->render(
			'@SS6Shop/Common/robots.txt.twig',
			[
				'sitemapUrl' => $sitemapUrl,
			],
			$response
		);
	}

}
