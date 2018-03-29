<?php

namespace Shopsys\ProductFeed\HeurekaBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\Plugin\PluginDataFixtureInterface;

class HeurekaProductDataFixture implements PluginDataFixtureInterface
{
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;
    const PRODUCT_ID_FOURTH = 4;
    const PRODUCT_ID_FIFTH = 5;

    /**
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade
     */
    private $heurekaProductDomainFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade
     */
    private $productFacade;

    /**
     * @param \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade $heurekaProductDomainFacade
     */
    public function __construct(HeurekaProductDomainFacade $heurekaProductDomainFacade, ProductFacade $productFacade)
    {
        $this->productFacade = $productFacade;
        $this->heurekaProductDomainFacade = $heurekaProductDomainFacade;
    }

    public function load()
    {
        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 12;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIRST, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIRST, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 3;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_SECOND, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 2;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_SECOND, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 1;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_THIRD, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 1;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_THIRD, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FOURTH, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 8;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FOURTH, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 10;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIFTH, $heurekaProductDomainData);

        $heurekaProductDomainData = new HeurekaProductDomainData();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIFTH, $heurekaProductDomainData);
    }
}
