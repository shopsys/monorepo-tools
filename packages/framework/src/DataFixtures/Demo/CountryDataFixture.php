<?php

namespace Shopsys\FrameworkBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\FrameworkBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\FrameworkBundle\Model\Country\CountryData;
use Shopsys\FrameworkBundle\Model\Country\CountryFacade;

class CountryDataFixture extends AbstractReferenceFixture
{
    const COUNTRY_CZECH_REPUBLIC_1 = 'country_czech_republic_1';
    const COUNTRY_SLOVAKIA_1 = 'country_slovakia_1';

    /** @var \Shopsys\FrameworkBundle\Model\Country\CountryFacade */
    private $countryFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryFacade $countryFacade
     */
    public function __construct(CountryFacade $countryFacade)
    {
        $this->countryFacade = $countryFacade;
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $domainId = 1;
        $countryData = new CountryData();
        $countryData->name = 'Czech republic';
        $countryData->code = 'CZ';
        $this->createCountry($countryData, $domainId, self::COUNTRY_CZECH_REPUBLIC_1);

        $countryData = new CountryData();
        $countryData->name = 'Slovakia';
        $countryData->code = 'SK';
        $this->createCountry($countryData, $domainId, self::COUNTRY_SLOVAKIA_1);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     * @param string $referenceName
     */
    private function createCountry(CountryData $countryData, $domainId, $referenceName)
    {
        $country = $this->countryFacade->create($countryData, $domainId);
        $this->addReference($referenceName, $country);
    }
}
