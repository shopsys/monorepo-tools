<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Component\DataFixture\AbstractReferenceFixture;
use SS6\ShopBundle\DataFixtures\Base\SettingValueDataFixture;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\UserData;

class UserDataFixture extends AbstractReferenceFixture implements DependentFixtureInterface {

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$registrationService = $this->get('ss6.shop.customer.registration_service');
		/* @var $registrationService \SS6\ShopBundle\Model\Customer\RegistrationService */

		$loaderService = $this->get('ss6.shop.data_fixtures.user_data_fixture_loader');
		/* @var $loaderService UserDataFixtureLoader */

		$customersData = $loaderService->getCustomersData();
		/* @var $customersData CustomerData[] */

		foreach ($customersData as $customerData) {
			if ($customerData->deliveryAddressData !== null) {
				$deliveryAddress = new DeliveryAddress($customerData->deliveryAddressData);
			} else {
				$deliveryAddress = null;
			}
			$this->createCustomer(
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
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies() {
		return array(
			SettingValueDataFixture::class,
		);
	}
}
