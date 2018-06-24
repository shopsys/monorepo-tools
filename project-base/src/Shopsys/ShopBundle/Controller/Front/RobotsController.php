<?php

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Sitemap\SitemapService;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Sitemap\SitemapService
     */
    private $sitemapService;

    public function __construct(
        Domain $domain,
        SitemapService $sitemapService
    ) {
        $this->domain = $domain;
        $this->sitemapService = $sitemapService;
    }

    public function indexAction()
    {
        $sitemapsUrlPrefix = $this->get('service_container')->getParameter('shopsys.sitemaps_url_prefix');
        $sitemapFilePrefix = $this->sitemapService->getSitemapFilePrefixForDomain($this->domain->getId());

        $sitemapUrl = $this->domain->getUrl() . $sitemapsUrlPrefix . '/' . $sitemapFilePrefix . '.xml';

        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');

        return $this->render(
            '@ShopsysFramework/Common/robots.txt.twig',
            [
                'sitemapUrl' => $sitemapUrl,
            ],
            $response
        );
    }
}
