<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Model\Country\Country;
use SS6\ShopBundle\Model\Country\CountryData;
use SS6\ShopBundle\Model\Country\CountryFacade;

class CountryDataFixture extends AbstractReferenceFixture {

	const COUNTRY_CZECH_REPUBLIC_2 = 'country_czech_republic_2';
	const COUNTRY_SLOVAKIA_2 = 'country_slovakia_2';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$domainId = 2;
		$countryData = new CountryData();
		$countryData->name = 'Czech republic';
		$this->createCountry($countryData, $domainId, self::COUNTRY_CZECH_REPUBLIC_2);

		$domainId = 2;
		$countryData = new CountryData();
		$countryData->name = 'Slovakia';
		$this->createCountry($countryData, $domainId, self::COUNTRY_SLOVAKIA_2);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Country\CountryData $countryData
	 * @param int $domainId
	 * @param string $referenceName
	 * @return \SS6\ShopBundle\Model\Country\Country
	 */
	private function createCountry(CountryData $countryData, $domainId, $referenceName) {
		$countryFacade = $this->get(CountryFacade::class);
		/* @var $countryFacade \SS6\ShopBundle\Model\Country\CountryFacade */

		$country = $countryFacade->create($countryData, $domainId);
		$this->addReference($referenceName, $country);
	}

}
