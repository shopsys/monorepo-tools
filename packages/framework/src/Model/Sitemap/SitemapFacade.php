<?php

namespace Shopsys\FrameworkBundle\Model\Sitemap;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;

class SitemapFacade
{
    /**
     * @var string
     */
    protected $sitemapsDir;

    /**
     * @var string
     */
    protected $sitemapsUrlPrefix;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Sitemap\SitemapDumperFactory
     */
    protected $domainSitemapDumperFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Sitemap\SitemapRepository
     */
    protected $sitemapRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    protected $pricingGroupSettingFacade;

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

    public function generateForAllDomains()
    {
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleProducts(DomainConfig $domainConfig)
    {
        $pricingGroup = $this->pricingGroupSettingFacade->getDefaultPricingGroupByDomainId($domainConfig->getId());

        return $this->sitemapRepository->getSitemapItemsForVisibleProducts($domainConfig, $pricingGroup);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForVisibleCategories(DomainConfig $domainConfig)
    {
        return $this->sitemapRepository->getSitemapItemsForVisibleCategories($domainConfig);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Sitemap\SitemapItem[]
     */
    public function getSitemapItemsForArticlesOnDomain(DomainConfig $domainConfig)
    {
        return $this->sitemapRepository->getSitemapItemsForArticlesOnDomain($domainConfig);
    }
}
