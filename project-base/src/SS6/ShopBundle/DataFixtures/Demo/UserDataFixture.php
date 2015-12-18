<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\UserData;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	const USER_PREFIX = 'user_';

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$registrationService = $this->get(RegistrationService::class);
		/* @var $registrationService \SS6\ShopBundle\Model\Customer\RegistrationService */

		$loaderService = $this->get(UserDataFixtureLoader::class);
		/* @var $loaderService \SS6\ShopBundle\DataFixtures\Demo\UserDataFixtureLoader */

		$customersData = $loaderService->getCustomersData();
		/* @var $customersData \SS6\ShopBundle\Model\Customer\CustomerData[] */

		foreach ($customersData as $index => $customerData) {
			if ($customerData->deliveryAddressData !== null) {
				$deliveryAddress = new DeliveryAddress($customerData->deliveryAddressData);
			} else {
				$deliveryAddress = null;
			}
			$this->createCustomer(
				self::USER_PREFIX . $index,
				$manager,
				$registrationService,
				$customerData->userData,
				new BillingAddress($customerData->billingAddressData),
				$deliveryAddress
			);
		}
		$manager->flush();
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 * @param \SS6\ShopBundle\Model\Customer\RegistrationService $registrationService
	 * @param \SS6\ShopBundle\Model\Customer\UserData $userData
	 * @param \SS6\ShopBundle\Model\Customer\BillingAddress $billingAddress
	 * @param \SS6\ShopBundle\Model\Customer\DeliveryAddress $deliveryAddress
	 */
	public function createCustomer(
		$referenceName,
		ObjectManager $manager,
		RegistrationService $registrationService,
		UserData $userData,
		BillingAddress $billingAddress,
		DeliveryAddress $deliveryAddress = null
	) {
		$user = $registrationService->create(
			$userData,
			$billingAddress,
			$deliveryAddress
		);

		$manager->persist($user);
		$manager->persist($billingAddress);
		if ($deliveryAddress !== null) {
			$manager->persist($deliveryAddress);
		}

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
