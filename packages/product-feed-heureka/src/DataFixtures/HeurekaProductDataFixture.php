<?php

namespace Shopsys\ProductFeed\HeurekaBundle\DataFixtures;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainDataFactoryInterface;
use Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainFacade;

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
     * @var \Shopsys\ProductFeed\HeurekaBundle\Model\Product\HeurekaProductDomainDataFactoryInterface
     */
    private $heurekaProductDomainDataFactory;

    public function __construct(
        HeurekaProductDomainFacade $heurekaProductDomainFacade,
        HeurekaProductDomainDataFactoryInterface $heurekaProductDomainDataFactory
    ) {
        $this->heurekaProductDomainFacade = $heurekaProductDomainFacade;
        $this->heurekaProductDomainDataFactory = $heurekaProductDomainDataFactory;
    }

    public function load()
    {
        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 12;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIRST, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIRST, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 3;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_SECOND, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 2;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_SECOND, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 1;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_THIRD, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 1;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_THIRD, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FOURTH, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 8;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FOURTH, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $heurekaProductDomainData->cpc = 10;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIFTH, $heurekaProductDomainData);

        $heurekaProductDomainData = $this->heurekaProductDomainDataFactory->create();
        $heurekaProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $heurekaProductDomainData->cpc = 5;

        $this->heurekaProductDomainFacade->saveHeurekaProductDomain(self::PRODUCT_ID_FIFTH, $heurekaProductDomainData);
    }
}
