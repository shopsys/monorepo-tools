<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\ShopBundle\Component\Controller\FrontBaseController;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Component\Sitemap\SitemapService;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends FrontBaseController
{
    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\ShopBundle\Component\Sitemap\SitemapService
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
        $sitemapsUrlPrefix = $this->get('service_container')->getParameter('shopsys.sitemaps_url_prefix');
        $sitemapFilePrefix = $this->sitemapService->getSitemapFilePrefixForDomain($this->domain->getId());

        $sitemapUrl = $this->domain->getUrl() . $sitemapsUrlPrefix . '/' . $sitemapFilePrefix . '.xml';

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        return $this->render(
            '@ShopsysShop/Common/robots.txt.twig',
            [
                'sitemapUrl' => $sitemapUrl,
            ],
            $response
        );
    }
}
