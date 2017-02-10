<?php

namespace Shopsys\ShopBundle\Component\Sitemap;

use Shopsys\ShopBundle\Component\Cron\CronModuleInterface;
use Shopsys\ShopBundle\Component\Sitemap\SitemapFacade;
use Symfony\Bridge\Monolog\Logger;

class SitemapCronModule implements CronModuleInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Sitemap\SitemapFacade
     */
    private $sitemapFacade;

    public function __construct(SitemapFacade $sitemapFacade) {
        $this->sitemapFacade = $sitemapFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger) {
    }

    public function run() {
        $this->sitemapFacade->generateForAllDomains();
    }
}
