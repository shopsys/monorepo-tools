<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Country\CountryDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;

class MultidomainCountryDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface
{
    const COUNTRY_CZECH_REPUBLIC = 'country_czech_republic';
    const COUNTRY_SLOVAKIA = 'country_slovakia';

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade
     */
    private $countryFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Country\CountryDataFactoryInterface
     */
    private $countryDataFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFacade $countryFacade
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryDataFactoryInterface $countryDataFactory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     */
    public function __construct(
        CountryFacade $countryFacade,
        CountryDataFactoryInterface $countryDataFactory,
        Domain $domain
    ) {
        $this->countryFacade = $countryFacade;
        $this->countryDataFactory = $countryDataFactory;
        $this->domain = $domain;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->domain->getAllIdsExcludingFirstDomain() as $domainId) {
            $this->loadForDomain($domainId);
        }
    }

    /**
     * @param int $domainId
     */
    private function loadForDomain(int $domainId)
    {
        $countryData = $this->countryDataFactory->create();
        $countryData->name = 'Česká republika';
        $countryData->code = 'CZ';
        $this->createCountry($countryData, $domainId, self::COUNTRY_CZECH_REPUBLIC);

        $countryData = $this->countryDataFactory->create();
        $countryData->name = 'Slovenská republika';
        $countryData->code = 'SK';
        $this->createCountry($countryData, $domainId, self::COUNTRY_SLOVAKIA);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     * @param string $referenceName
     */
    private function createCountry(CountryData $countryData, int $domainId, string $referenceName)
    {
        $country = $this->countryFacade->create($countryData, $domainId);
        $this->addReferenceForDomain($referenceName, $country, $domainId);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            CountryDataFixture::class,
        ];
    }
}
