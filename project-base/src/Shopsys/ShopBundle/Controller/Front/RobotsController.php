<?php

declare(strict_types=1);

namespace Shopsys\ShopBundle\Controller\Front;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Sitemap\SitemapFilePrefixer;
use Symfony\Component\HttpFoundation\Response;

class RobotsController extends FrontBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Sitemap\SitemapFilePrefixer
     */
    private $sitemapFilePrefixer;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Sitemap\SitemapFilePrefixer $sitemapFilePrefixer
     */
    public function __construct(
        Domain $domain,
        SitemapFilePrefixer $sitemapFilePrefixer
    ) {
        $this->domain = $domain;
        $this->sitemapFilePrefixer = $sitemapFilePrefixer;
    }

    public function indexAction()
    {
        $sitemapsUrlPrefix = $this->get('service_container')->getParameter('shopsys.sitemaps_url_prefix');
        $sitemapFilePrefix = $this->sitemapFilePrefixer->getSitemapFilePrefixForDomain($this->domain->getId());

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
