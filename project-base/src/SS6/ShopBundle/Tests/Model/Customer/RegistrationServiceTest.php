<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use DateTime;
use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\DeliveryAddress;
use SS6\ShopBundle\Model\Customer\DeliveryAddressData;
use SS6\ShopBundle\Model\Customer\RegistrationService;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;

class RegistrationServiceTest extends FunctionalTestCase {

	public function testCreate() {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');
		$hashGenerator = $this->getContainer()->get('ss6.shop.component.string.hash_generator');

		$registrationService = new RegistrationService(
			$encoderFactory,
			$hashGenerator
		);

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
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');
		$hashGenerator = $this->getContainer()->get('ss6.shop.component.string.hash_generator');

		$registrationService = new RegistrationService(
			$encoderFactory,
			$hashGenerator
		);

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
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');
		$hashGenerator = $this->getContainer()->get('ss6.shop.component.string.hash_generator');

		$registrationService = new RegistrationService(
			$encoderFactory,
			$hashGenerator
		);

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
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');
		$hashGenerator = $this->getContainer()->get('ss6.shop.component.string.hash_generator');

		$registrationService = new RegistrationService(
			$encoderFactory,
			$hashGenerator
		);

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

	public function isResetPasswordHashValidProvider() {
		return [
			[
				'resetPasswordHash' => 'validHash',
				'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
				'sentHash' => 'validHash',
				'isExpectedValid' => true,
			],
			[
				'resetPasswordHash' => null,
				'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
				'sentHash' => 'hash',
				'isExpectedValid' => false,
			],
			[
				'resetPasswordHash' => 'validHash',
				'resetPasswordHashValidThrough' => new DateTime('+1 hour'),
				'sentHash' => 'invalidHash',
				'isExpectedValid' => false,
			],
			[
				'resetPasswordHash' => 'validHash',
				'resetPasswordHashValidThrough' => null,
				'sentHash' => 'validHash',
				'isExpectedValid' => false,
			],
			[
				'resetPasswordHash' => 'validHash',
				'resetPasswordHashValidThrough' => new DateTime('-1 hour'),
				'sentHash' => 'validHash',
				'isExpectedValid' => false,
			],
		];
	}

	/**
	 * @dataProvider isResetPasswordHashValidProvider
	 */
	public function testIsResetPasswordHashValid(
		$resetPasswordHash,
		$resetPasswordHashValidThrough,
		$sentHash,
		$isExpectedValid
	) {
		$encoderFactory = $this->getContainer()->get('security.encoder_factory');
		$hashGenerator = $this->getContainer()->get('ss6.shop.component.string.hash_generator');

		$registrationService = new RegistrationService(
			$encoderFactory,
			$hashGenerator
		);

		$userMock = $this->getMockBuilder(User::class)
			->disableOriginalConstructor()
			->setMethods(['getResetPasswordHash', 'getResetPasswordHashValidThrough'])
			->getMock();

		$userMock->expects($this->any())->method('getResetPasswordHash')
			->willReturn($resetPasswordHash);
		$userMock->expects($this->any())->method('getResetPasswordHashValidThrough')
			->willReturn($resetPasswordHashValidThrough);

		$isResetPasswordHashValid = $registrationService->isResetPasswordHashValid($userMock, $sentHash);

		$this->assertEquals($isExpectedValid, $isResetPasswordHashValid);
	}

}
