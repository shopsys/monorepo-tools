<?php

namespace Shopsys\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use Shopsys\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Country\Country;
use Shopsys\ShopBundle\Model\Country\CountryData;
use Shopsys\ShopBundle\Model\Country\CountryFacade;

class CountryDataFixture extends AbstractReferenceFixture {

    const COUNTRY_CZECH_REPUBLIC_1 = 'country_czech_republic_1';
    const COUNTRY_SLOVAKIA_1 = 'country_slovakia_1';

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        $this->addCzechCountryReference($manager);
        $this->loadSlovakCountry();
    }

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    private function addCzechCountryReference(ObjectManager $manager) {
        $country = $manager->getRepository(Country::class)->findOneBy(['domainId' => Domain::FIRST_DOMAIN_ID]);
        $this->addReference(self::COUNTRY_CZECH_REPUBLIC_1, $country);
    }

    private function loadSlovakCountry() {
        $domainId = 1;
        $countryData = new CountryData();
        $countryData->name = 'Slovenská republika';
        $country = $this->createCountry($countryData, $domainId);
        $this->addReference(self::COUNTRY_SLOVAKIA_1, $country);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Country\CountryData $countryData
     * @param int $domainId
     * @return \Shopsys\ShopBundle\Model\Country\Country
     */
    private function createCountry(CountryData $countryData, $domainId) {
        $countryFacade = $this->get(CountryFacade::class);
        /* @var $countryFacade \Shopsys\ShopBundle\Model\Country\CountryFacade */

        return $countryFacade->create($countryData, $domainId);
    }

}
