<?php

namespace Shopsys\ProductFeed\ZboziBundle\DataFixtures;

use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainDataFactoryInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;

class ZboziPluginDataFixture implements PluginDataFixtureInterface
{
    const DOMAIN_ID_FIRST = 1;
    const DOMAIN_ID_SECOND = 2;
    const PRODUCT_ID_FIRST = 1;
    const PRODUCT_ID_SECOND = 2;
    const PRODUCT_ID_THIRD = 3;
    const PRODUCT_ID_FOURTH = 4;
    const PRODUCT_ID_FIFTH = 5;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade
     */
    private $zboziProductDomainFacade;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainDataFactoryInterface
     */
    private $zboziProductDomainDataFactory;

    public function __construct(
        ZboziProductDomainFacade $zboziProductDomainFacade,
        ZboziProductDomainDataFactoryInterface $zboziProductDomainDataFactory
    ) {
        $this->zboziProductDomainFacade = $zboziProductDomainFacade;
        $this->zboziProductDomainDataFactory = $zboziProductDomainDataFactory;
    }

    public function load()
    {
        $firstZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $firstZboziProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $firstZboziProductDomainData->cpc = 15;
        $firstZboziProductDomainData->cpcSearch = 8;
        $firstZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FIRST, $firstZboziProductDomainData);

        $secondZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $secondZboziProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $secondZboziProductDomainData->cpc = 12;
        $secondZboziProductDomainData->cpcSearch = 15;
        $secondZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FIRST, $secondZboziProductDomainData);

        $thirdZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $thirdZboziProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $thirdZboziProductDomainData->cpc = 5;
        $thirdZboziProductDomainData->cpcSearch = 3;
        $thirdZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_SECOND, $thirdZboziProductDomainData);

        $fourthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $fourthZboziProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $fourthZboziProductDomainData->cpc = 20;
        $fourthZboziProductDomainData->cpcSearch = 5;
        $fourthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_SECOND, $fourthZboziProductDomainData);

        $fifthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $fifthZboziProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $fifthZboziProductDomainData->cpc = 10;
        $fifthZboziProductDomainData->cpcSearch = 5;
        $fifthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_THIRD, $fifthZboziProductDomainData);

        $sixthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $sixthZboziProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $sixthZboziProductDomainData->cpc = 15;
        $sixthZboziProductDomainData->cpcSearch = 7;
        $sixthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_THIRD, $sixthZboziProductDomainData);

        $seventhZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $seventhZboziProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $seventhZboziProductDomainData->cpc = 9;
        $seventhZboziProductDomainData->cpcSearch = 8;
        $seventhZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FOURTH, $seventhZboziProductDomainData);

        $eighthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $eighthZboziProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $eighthZboziProductDomainData->cpc = 4;
        $eighthZboziProductDomainData->cpcSearch = 3;
        $eighthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FOURTH, $eighthZboziProductDomainData);

        $ninthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $ninthZboziProductDomainData->domainId = self::DOMAIN_ID_FIRST;
        $ninthZboziProductDomainData->cpc = 4;
        $ninthZboziProductDomainData->cpcSearch = 2;
        $ninthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FIFTH, $ninthZboziProductDomainData);

        $tenthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $tenthZboziProductDomainData->domainId = self::DOMAIN_ID_SECOND;
        $tenthZboziProductDomainData->cpc = 5;
        $tenthZboziProductDomainData->cpcSearch = 6;
        $tenthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(self::PRODUCT_ID_FIFTH, $tenthZboziProductDomainData);
    }
}
