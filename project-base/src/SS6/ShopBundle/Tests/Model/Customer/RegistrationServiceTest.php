<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressFormData;
use SS6\ShopBundle\Model\Customer\UserFormData;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressFormData;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\User;

class RegistrationServiceTest extends FunctionalTestCase {

	public function testCreate() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress = new BillingAddress(new BillingAddressFormData());
		$deliveryAddress = new DeliveryAddress(new DeliveryAddressFormData());
		$userByEmail = null;
		$userFormData = new UserFormData();
		$userFormData->setFirstName('firstName');
		$userFormData->setLastName('lastName');
		$userFormData->setEmail('no-reply@netdevelo.cz');
		$userFormData->setPassword('pa55w0rd');

		$user = $registrationService->create(
			$userFormData,
			$billingAddress,
			$deliveryAddress,
			$userByEmail);

		$this->assertInstanceOf(User::class, $user);
	}

	public function testCreateNotDuplicateEmail() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress1 = new BillingAddress(new BillingAddressFormData());
		$deliveryAddress1 = new DeliveryAddress(new DeliveryAddressFormData());
		$userByEmail = null;
		$userFormData1 = new UserFormData();
		$userFormData1->setFirstName('firstName1');
		$userFormData1->setLastName('lastName2');
		$userFormData1->setEmail('no-reply@netdevelo.cz');
		$userFormData1->setPassword('pa55w0rd');

		$user1 = $registrationService->create(
			$userFormData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);
		$this->assertInstanceOf(User::class, $user1);

		$billingAddress2 = new BillingAddress(new BillingAddressFormData());
		$deliveryAddress2 = new DeliveryAddress(new DeliveryAddressFormData());
		$userFormData2 = new UserFormData();
		$userFormData2->setFirstName('firstName1');
		$userFormData2->setLastName('lastName2');
		$userFormData2->setEmail('no-reply2@netdevelo.cz');
		$userFormData2->setPassword('pa55w0rd');

		$user2 = $registrationService->create(
			$userFormData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1);
		$this->assertInstanceOf(User::class, $user2);
	}

	public function testCreateDuplicateEmail() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');

		$registrationService = new RegistrationService($encoderFactory);

		$billingAddress1 = new BillingAddress(new BillingAddressFormData());
		$deliveryAddress1 = new DeliveryAddress(new DeliveryAddressFormData());
		$userByEmail = null;
		$userFormData1 = new UserFormData();
		$userFormData1->setFirstName('firstName1');
		$userFormData1->setLastName('lastName2');
		$userFormData1->setEmail('no-reply@netdevelo.cz');
		$userFormData1->setPassword('pa55w0rd');

		$user1 = $registrationService->create(
			$userFormData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail);

		$billingAddress2 = new BillingAddress(new BillingAddressFormData());
		$deliveryAddress2 = new DeliveryAddress(new DeliveryAddressFormData());
		$userFormData2 = new UserFormData();
		$userFormData2->setFirstName('firstName2');
		$userFormData2->setLastName('lastName2');
		$userFormData2->setEmail('no-reply@netdevelo.cz');
		$userFormData2->setPassword('pa55w0rd');

		$this->setExpectedException(\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException::class);
		$registrationService->create(
			$userFormData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1);
	}

}
