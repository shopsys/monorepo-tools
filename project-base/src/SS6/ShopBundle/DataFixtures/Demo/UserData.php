<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface {
	
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

		$billingAddress = new BillingAddress('Hlubinská 36', 'Ostrava', '70200', 'Czech Republic',
			'netdevelo s.r.o.', '123456789', '987654321', '+420123456789');
		$deliveryAddress = new DeliveryAddress('Slévárenská 18/408', 'Ostrava', '70900', 'Czech Republic',
			'netdevelo s.r.o,', 'John Doe', '+420987654321');

		$user = $registrationService->create(
			'John',
			'Watson',
			'no-reply@netdevelo.cz',
			'user123',
			$billingAddress,
			$deliveryAddress);

		$manager->persist($billingAddress);
		$manager->persist($deliveryAddress);
		$manager->persist($user);
		$manager->flush();
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}
