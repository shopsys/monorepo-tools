<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\CustomerFormData;
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
		$customerFormData = new CustomerFormData();
		$customerFormData->setFirstName('firstName');
		$customerFormData->setLastName('lastName');
		$customerFormData->setEmail('no-reply@netdevelo.cz');
		$customerFormData->setPassword('pa55w0rd');

		$user = $registrationService->create(
			$customerFormData,
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
		$customerFormData1 = new CustomerFormData();
		$customerFormData1->setFirstName('firstName1');
		$customerFormData1->setLastName('lastName2');
		$customerFormData1->setEmail('no-reply@netdevelo.cz');
		$customerFormData1->setPassword('pa55w0rd');

		$user1 = $registrationService->create(
			$customerFormData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);
		$this->assertInstanceOf(User::class, $user1);

		$billingAddress2 = new BillingAddress();
		$deliveryAddress2 = new DeliveryAddress();
		$customerFormData2 = new CustomerFormData();
		$customerFormData2->setFirstName('firstName1');
		$customerFormData2->setLastName('lastName2');
		$customerFormData2->setEmail('no-reply2@netdevelo.cz');
		$customerFormData2->setPassword('pa55w0rd');

		$user2 = $registrationService->create(
			$customerFormData2,
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
		$customerFormData1 = new CustomerFormData();
		$customerFormData1->setFirstName('firstName1');
		$customerFormData1->setLastName('lastName2');
		$customerFormData1->setEmail('no-reply@netdevelo.cz');
		$customerFormData1->setPassword('pa55w0rd');

		$user1 = $registrationService->create(
			$customerFormData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);

		$billingAddress2 = new BillingAddress();
		$deliveryAddress2 = new DeliveryAddress();
		$customerFormData2 = new CustomerFormData();
		$customerFormData2->setFirstName('firstName2');
		$customerFormData2->setLastName('lastName2');
		$customerFormData2->setEmail('no-reply@netdevelo.cz');
		$customerFormData2->setPassword('pa55w0rd');

		$this->setExpectedException(\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException::class);
		$registrationService->create(
			$customerFormData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1);
	}

}
