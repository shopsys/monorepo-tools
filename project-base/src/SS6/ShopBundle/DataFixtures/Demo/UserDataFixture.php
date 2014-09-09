<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserDataFixture extends AbstractFixture implements ContainerAwareInterface {
	
	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	private $container;
	
	/**
	 * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
	 */
	public function setContainer(ContainerInterface $container = null) {
		$this->container = $container;
	}

	/**
	 * @param \Doctrine\Common\Persistence\ObjectManager $manager
	 */
	public function load(ObjectManager $manager) {
		$registrationService = $this->container->get('ss6.shop.customer.registration_service');
		/* @var $registrationService \SS6\ShopBundle\Model\Customer\RegistrationService */

		$loaderService = $this->container->get('ss6.shop.data_fixtures.user_data_fixture_loader');
		/* @var $loaderService UserDataFixtureLoader */

		$array = $loaderService->getUsersData();

		foreach ($array as $record) {
			$this->createCustomer($manager, $registrationService, $record['user'], $record['billing'], $record['delivery']);
		}
		$manager->flush();
	}

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
}
