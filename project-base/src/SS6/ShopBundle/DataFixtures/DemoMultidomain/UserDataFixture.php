<?php

namespace SS6\ShopBundle\DataFixtures\DemoMultidomain;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader;
use SS6\ShopBundle\DataFixtures\DemoMultidomain\CountryDataFixture;
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
			$this->getReference(CountryDataFixture::COUNTRY_CZECH_REPUBLIC_2),
			$this->getReference(CountryDataFixture::COUNTRY_SLOVAKIA_2),
		];
		$loaderService->injectReferences($countries);

		$customersData = $loaderService->getCustomersDataByDomainId(2);

		foreach ($customersData as $customerData) {
			$customerFacade->create($customerData);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getDependencies() {
		return [
			CountryDataFixture::class,
		];
	}

}
