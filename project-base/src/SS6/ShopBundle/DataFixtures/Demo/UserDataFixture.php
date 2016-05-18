<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader;
use SS6\ShopBundle\Model\Customer\CustomerData;
use SS6\ShopBundle\Model\Customer\CustomerFacade;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const USER_PREFIX = 'user_';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$loaderService = $this->get(UserDataFixtureLoader::class);
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader */

		$customersData = $loaderService->getCustomersData();
		/* @var $customersData \SS6\ShopBundle\Model\Customer\CustomerData[] */

		foreach ($customersData as $index => $customerData) {
			$this->createCustomer($customerData, self::USER_PREFIX . $index);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerData $customerData
	 * @param string $referenceName
	 */
	private function createCustomer(CustomerData $customerData, $referenceName = null) {
		$customerFacade = $this->get(CustomerFacade::class);
		/* @var $customerFacade \SS6\ShopBundle\Model\Customer\CustomerFacade */

		$user = $customerFacade->create($customerData);
		$this->addReference($referenceName, $user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return [
			SettingValueDataFixture::class,
		];
	}
}
