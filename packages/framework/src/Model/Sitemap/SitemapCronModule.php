<?php

namespace Shopsys\FrameworkBundle\Model\Sitemap;

use Shopsys\Plugin\Cron\SimpleCronModuleInterface;
use Symfony\Bridge\Monolog\Logger;

class SitemapCronModule implements SimpleCronModuleInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Sitemap\SitemapFacade
     */
    private $sitemapFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Sitemap\SitemapFacade $sitemapFacade
     */
    public function __construct(SitemapFacade $sitemapFacade)
    {
        $this->sitemapFacade = $sitemapFacade;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(Logger $logger)
    {
    }

    public function run()
    {
        $this->sitemapFacade->generateForAllDomains();
    }
}
