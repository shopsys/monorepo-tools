<?php

namespace SS6\ShopBundle\DataFixtures\Demo;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\RegistrationService;
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

		$this->createCustomer($manager, $registrationService,
			'John',
			'Watson',
			'no-reply@netdevelo.cz',
			'user123',
			'Hlubinská 36',
			'Ostrava',
			'70200',
			'Czech Republic',
			'netdevelo s.r.o.',
			'123456789',
			'987654321',
			'+420123456789',
			'Slévárenská 18/408',
			'Ostrava',
			'70900',
			'Czech Republic',
			'netdevelo s.r.o.',
			'John Doe',
			'+420987654321'
		);

		$this->createCustomer($manager, $registrationService,
			'Kerluke',
			'Bill',
			'Carole@maida.biz',
			'asdfasdf',
			'65597 Candido Cape',
			'Larkinside',
			'72984-3630',
			'Taiwan',
			null,
			null,
			null,
			'1-478-693-5236 x8701',
			'91147 Reinger Via',
			'Blandaville',
			'60081',
			'Syria',
			null,
			'Sporer Leda',
			'576-124-5478 x1457'
		);

		$manager->flush();
	}

	public function createCustomer(ObjectManager $manager, RegistrationService $registrationService,
			$firstName, $lastName, $email, $password,
			$billingStreet = null, $billingCity = null, $billingPostcode = null, $billingCountry = null,
			$billingCompanyName = null, $billingCompanyNumber = null, $billingCompanyTaxNumber = null,
			$billingTelephone = null,
			$deliveryStreet = null, $deliveryCity = null, $deliveryPostcode = null, $deliveryCountry = null,
			$deliveryCompanyName = null, $deliveryContactPerson = null, $deliveryTelephone = null) {

		$billingAddress = new BillingAddress(
			$billingStreet,
			$billingCity,
			$billingPostcode,
			$billingCountry,
			$billingCompanyName,
			$billingCompanyNumber,
			$billingCompanyTaxNumber,
			$billingTelephone
		);
		$deliveryAddress = new DeliveryAddress(
			$deliveryStreet,
			$deliveryCity,
			$deliveryPostcode,
			$deliveryCountry,
			$deliveryCompanyName,
			$deliveryContactPerson,
			$deliveryTelephone
		);

		$user = $registrationService->create(
			$firstName,
			$lastName,
			$email,
			$password,
			$billingAddress,
			$deliveryAddress
		);

		$manager->persist($billingAddress);
		$manager->persist($deliveryAddress);
		$manager->persist($user);
	}

	/**
	 * @return int
	 */
	public function getOrder() {
		return 1;
	}	
}
