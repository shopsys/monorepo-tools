<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\CustomerPasswordService;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;

class RegistrationServiceTest extends PHPUnit_Framework_TestCase {

	public function testCreate() {
		$registrationService = $this->getRegistrationService();

		$billingAddress = new BillingAddress(new BillingAddressData());
		$deliveryAddress = new DeliveryAddress(new DeliveryAddressData());
		$userByEmail = null;
		$userData = new UserData();
		$userData->firstName = 'firstName';
		$userData->lastName = 'lastName';
		$userData->email = 'no-reply@netdevelo.cz';
		$userData->password = 'pa55w0rd';

		$user = $registrationService->create(
			$userData,
			$billingAddress,
			$deliveryAddress,
			$userByEmail
		);

		$this->assertInstanceOf(User::class, $user);
	}

	public function testCreateNotDuplicateEmail() {
		$registrationService = $this->getRegistrationService();

		$billingAddress1 = new BillingAddress(new BillingAddressData());
		$deliveryAddress1 = new DeliveryAddress(new DeliveryAddressData());
		$userByEmail = null;
		$userData1 = new UserData();
		$userData1->firstName = 'firstName1';
		$userData1->lastName = 'lastName1';
		$userData1->email = 'no-reply@netdevelo.cz';
		$userData1->password = 'pa55w0rd';

		$user1 = $registrationService->create(
			$userData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail
		);
		$this->assertInstanceOf(User::class, $user1);

		$billingAddress2 = new BillingAddress(new BillingAddressData());
		$deliveryAddress2 = new DeliveryAddress(new DeliveryAddressData());
		$userData2 = new UserData();
		$userData2->firstName = 'firstName2';
		$userData2->lastName = 'lastName2';
		$userData2->email = 'no-reply2@netdevelo.cz';
		$userData2->password = 'pa55w0rd';

		$user2 = $registrationService->create(
			$userData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1
		);
		$this->assertInstanceOf(User::class, $user2);
	}

	public function testCreateDuplicateEmail() {
		$registrationService = $this->getRegistrationService();

		$billingAddress1 = new BillingAddress(new BillingAddressData());
		$deliveryAddress1 = new DeliveryAddress(new DeliveryAddressData());
		$userByEmail = null;
		$userData1 = new UserData();
		$userData1->firstName = 'firstName1';
		$userData1->lastName = 'lastName1';
		$userData1->email = 'no-reply@netdevelo.cz';
		$userData1->password = 'pa55w0rd';

		$user1 = $registrationService->create(
			$userData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail
		);

		$billingAddress2 = new BillingAddress(new BillingAddressData());
		$deliveryAddress2 = new DeliveryAddress(new DeliveryAddressData());
		$userData2 = new UserData();
		$userData2->firstName = 'firstName2';
		$userData2->lastName = 'lastName2';
		$userData2->email = 'no-reply@netdevelo.cz';
		$userData2->password = 'pa55w0rd';

		$this->setExpectedException(\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException::class);
		$registrationService->create(
			$userData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1
		);
	}

	public function testCreateDuplicateEmailCaseInsentitive() {
		$registrationService = $this->getRegistrationService();

		$billingAddress1 = new BillingAddress(new BillingAddressData());
		$deliveryAddress1 = new DeliveryAddress(new DeliveryAddressData());
		$userByEmail = null;
		$userData1 = new UserData();
		$userData1->firstName = 'firstName1';
		$userData1->lastName = 'lastName1';
		$userData1->email = 'no-reply@netdevelo.cz';
		$userData1->password = 'pa55w0rd';

		$user1 = $registrationService->create(
			$userData1,
			$billingAddress1,
			$deliveryAddress1,
			$userByEmail
		);

		$billingAddress2 = new BillingAddress(new BillingAddressData());
		$deliveryAddress2 = new DeliveryAddress(new DeliveryAddressData());
		$userData2 = new UserData();
		$userData2->firstName = 'firstName2';
		$userData2->lastName = 'lastName2';
		$userData2->email = 'NO-reply@netdevelo.cz';
		$userData2->password = 'pa55w0rd';

		$this->setExpectedException(\SS6\ShopBundle\Model\Customer\Exception\DuplicateEmailException::class);
		$registrationService->create(
			$userData2,
			$billingAddress2,
			$deliveryAddress2,
			$user1
		);
	}

	/**
	 * @return \SS6\ShopBundle\Model\Customer\RegistrationService
	 */
	private function getRegistrationService() {
		$customerPasswordServiceMock = $this->getMock(CustomerPasswordService::class, [], [], '', false);

		return new RegistrationService($customerPasswordServiceMock);
	}

}
