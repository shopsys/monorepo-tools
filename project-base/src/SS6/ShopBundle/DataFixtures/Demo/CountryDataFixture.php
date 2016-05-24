<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Country\Country;
use SS6\ShopBundle\Model\Country\CountryData;
use SS6\ShopBundle\Model\Country\CountryFacade;

class CountryDataFixture extends AbstractReferenceFixture {

	const COUNTRY_CZECH_REPUBLIC_1 = 'country_czech_republic_1';
	const COUNTRY_SLOVAKIA_1 = 'country_slovakia_1';
	const COUNTRY_CZECH_REPUBLIC_2 = 'country_czech_republic_2';
	const COUNTRY_SLOVAKIA_2 = 'country_slovakia_2';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$this->addCzechCountriesReferences($manager);
		$this->loadSlovakCountries();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	private function addCzechCountriesReferences(ObjectManager $manager) {
		$domainId = 1;
		$country = $manager->getRepository(Country::class)->findOneBy(['domainId' => $domainId]);
		$this->addReference(self::COUNTRY_CZECH_REPUBLIC_1, $country);

		$domainId = 2;
		$country = $manager->getRepository(Country::class)->findOneBy(['domainId' => $domainId]);
		$this->addReference(self::COUNTRY_CZECH_REPUBLIC_2, $country);
	}

	private function loadSlovakCountries() {
		$domainId = 1;
		$countryData = new CountryData();
		$countryData->name = 'Slovenská republika';
		$country = $this->createCountry($countryData, $domainId);
		$this->addReference(self::COUNTRY_SLOVAKIA_1, $country);

		$domainId = 2;
		$countryData = new CountryData();
		$countryData->name = 'Slovakia';
		$country = $this->createCountry($countryData, $domainId);
		$this->addReference(self::COUNTRY_SLOVAKIA_2, $country);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @param int $domainId
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	private function createCountry(CountryData $countryData, $domainId) {
		$countryFacade = $this->get(CountryFacade::class);
		/* @var $countryFacade \SS6\ShopBundle\Model\Country\CountryFacade */

		return $countryFacade->create($countryData, $domainId);
	}

}
