<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\CountryDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader;
use SS6\ShopBundle\Model\Customer\CustomerFacade;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$customerFacade = $this->get(CustomerFacade::class);
		/* @var $customerFacade \SS6\ShopBundle\Model\Customer\CustomerFacade */
		$loaderService = $this->get(UserDataFixtureLoader::class);
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader */

		$countries = [
			$this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_1),
			$this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_1),
		];
		$loaderService->injectReferences($countries);

		$customersData = $loaderService->getCustomersDataByDomainId(Domain::FIRST_DOMAIN_ID);

		foreach ($customersData as $customerData) {
			$customerFacade->create($customerData);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			SettingValueDataFixture::class,
			CountryDataFixture::class,
		];
	}
}
