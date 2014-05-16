<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\User;

class RegistrationServiceTest extends FunctionalTestCase {

	public function testCreate() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress = new BillingAddress();
		$deliveryAddress = new DeliveryAddress();
		$userByEmail = null;
		$user = $registrationService->create(
			'firstName',
			'lastName',
			'no-reply@netdevelo.cz',
			'pa55w0rd',
			$billingAddress,
			$deliveryAddress,
			$userByEmail);

		$this->assertInstanceOf(User::class, $user);
	}

	public function testCreateNotDuplicateEmail() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress1 = new BillingAddress();
		$deliveryAddress1 = new DeliveryAddress();
		$userByEmail = null;
		$user1 = $registrationService->create('firstName1',
			'lastName2',
			'no-reply@netdevelo.cz',
			'pa55w0rd',
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);
		$this->assertInstanceOf(User::class, $user1);

		$billingAddress2 = new BillingAddress();
		$deliveryAddress2 = new DeliveryAddress();
		$user2 = $registrationService->create(
			'firstName1',
			'lastName2',
			'no-reply2@netdevelo.cz',
			'pa55w0rd',
			$billingAddress2,
			$deliveryAddress2,
			$user1);
		$this->assertInstanceOf(User::class, $user2);
	}

	public function testCreateDuplicateEmail() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress1 = new BillingAddress();
		$deliveryAddress1 = new DeliveryAddress();
		$userByEmail = null;
		$user1 = $registrationService->create(
			'firstName1',
			'lastName2',
			'no-reply@netdevelo.cz',
			'pa55w0rd',
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);

		$billingAddress2 = new BillingAddress();
		$deliveryAddress2 = new DeliveryAddress();

		$this->setExpectedException(\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException::class);
		$registrationService->create(
			'firstName2',
			'lastName2',
			'no-reply@netdevelo.cz',
			'pa55w0rd',
			$billingAddress2,
			$deliveryAddress2,
			$user1);
	}

}
