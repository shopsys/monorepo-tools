<?php

namespace Shopsys\ProductFeed\ZboziBundle\DataFixtures;

use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\Plugin\PluginDataFixtureInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainDataFactoryInterface;
use Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade;

class ZboziPluginDataFixture implements PluginDataFixtureInterface
{
    protected const DOMAIN_ID_FIRST = 1;
    protected const DOMAIN_ID_SECOND = 2;
    protected const PRODUCT_ID_FIRST = 1;
    protected const PRODUCT_ID_SECOND = 2;
    protected const PRODUCT_ID_THIRD = 3;
    protected const PRODUCT_ID_FOURTH = 4;
    protected const PRODUCT_ID_FIFTH = 5;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade
     */
    private $zboziProductDomainFacade;

    /**
     * @var \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainDataFactoryInterface
     */
    private $zboziProductDomainDataFactory;

    /**
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainFacade $zboziProductDomainFacade
     * @param \Shopsys\ProductFeed\ZboziBundle\Model\Product\ZboziProductDomainDataFactoryInterface $zboziProductDomainDataFactory
     */
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
        $firstZboziProductDomainData->domainId = static::DOMAIN_ID_FIRST;
        $firstZboziProductDomainData->cpc = Money::create(15);
        $firstZboziProductDomainData->cpcSearch = Money::create(8);
        $firstZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FIRST, $firstZboziProductDomainData);

        $secondZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $secondZboziProductDomainData->domainId = static::DOMAIN_ID_SECOND;
        $secondZboziProductDomainData->cpc = Money::create(12);
        $secondZboziProductDomainData->cpcSearch = Money::create(15);
        $secondZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FIRST, $secondZboziProductDomainData);

        $thirdZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $thirdZboziProductDomainData->domainId = static::DOMAIN_ID_FIRST;
        $thirdZboziProductDomainData->cpc = Money::create(5);
        $thirdZboziProductDomainData->cpcSearch = Money::create(3);
        $thirdZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_SECOND, $thirdZboziProductDomainData);

        $fourthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $fourthZboziProductDomainData->domainId = static::DOMAIN_ID_SECOND;
        $fourthZboziProductDomainData->cpc = Money::create(20);
        $fourthZboziProductDomainData->cpcSearch = Money::create(5);
        $fourthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_SECOND, $fourthZboziProductDomainData);

        $fifthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $fifthZboziProductDomainData->domainId = static::DOMAIN_ID_FIRST;
        $fifthZboziProductDomainData->cpc = Money::create(10);
        $fifthZboziProductDomainData->cpcSearch = Money::create(5);
        $fifthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_THIRD, $fifthZboziProductDomainData);

        $sixthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $sixthZboziProductDomainData->domainId = static::DOMAIN_ID_SECOND;
        $sixthZboziProductDomainData->cpc = Money::create(15);
        $sixthZboziProductDomainData->cpcSearch = Money::create(7);
        $sixthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_THIRD, $sixthZboziProductDomainData);

        $seventhZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $seventhZboziProductDomainData->domainId = static::DOMAIN_ID_FIRST;
        $seventhZboziProductDomainData->cpc = Money::create(9);
        $seventhZboziProductDomainData->cpcSearch = Money::create(8);
        $seventhZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FOURTH, $seventhZboziProductDomainData);

        $eighthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $eighthZboziProductDomainData->domainId = static::DOMAIN_ID_SECOND;
        $eighthZboziProductDomainData->cpc = Money::create(4);
        $eighthZboziProductDomainData->cpcSearch = Money::create(3);
        $eighthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FOURTH, $eighthZboziProductDomainData);

        $ninthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $ninthZboziProductDomainData->domainId = static::DOMAIN_ID_FIRST;
        $ninthZboziProductDomainData->cpc = Money::create(4);
        $ninthZboziProductDomainData->cpcSearch = Money::create(2);
        $ninthZboziProductDomainData->show = true;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FIFTH, $ninthZboziProductDomainData);

        $tenthZboziProductDomainData = $this->zboziProductDomainDataFactory->create();
        $tenthZboziProductDomainData->domainId = static::DOMAIN_ID_SECOND;
        $tenthZboziProductDomainData->cpc = Money::create(5);
        $tenthZboziProductDomainData->cpcSearch = Money::create(6);
        $tenthZboziProductDomainData->show = false;

        $this->zboziProductDomainFacade->saveZboziProductDomain(static::PRODUCT_ID_FIFTH, $tenthZboziProductDomainData);
    }
}
