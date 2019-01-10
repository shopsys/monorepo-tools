<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress;
use Shopsys\FrameworkBundle\Model\Customer\DeliveryAddressData;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;

class UserTest extends TestCase
{
    public function testGetFullNameReturnsLastnameAndFirstnameForUser()
    {
        $userData = new UserData();
        $userData->firstName = 'Firstname';
        $userData->lastName = 'Lastname';
        $userData->email = 'no-reply@shopsys.com';
        $billingAddress = $this->createBillingAddress();
        $user = new User($userData, $billingAddress, null, null);

        $this->assertSame('Lastname Firstname', $user->getFullName());
    }

    public function testGetFullNameReturnsCompanyNameForCompanyUser()
    {
        $userData = new UserData();
        $userData->firstName = 'Firstname';
        $userData->lastName = 'Lastname';
        $userData->email = 'no-reply@shopsys.com';
        $billingAddressData = new BillingAddressData();
        $billingAddressData->companyCustomer = true;
        $billingAddressData->companyName = 'CompanyName';
        $billingAddress = new BillingAddress($billingAddressData);
        $user = new User($userData, $billingAddress, null, null);

        $this->assertSame('CompanyName', $user->getFullName());
    }

    public function isResetPasswordHashValidProvider()
    {
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
     * @param mixed $resetPasswordHash
     * @param mixed $resetPasswordHashValidThrough
     * @param mixed $sentHash
     * @param mixed $isExpectedValid
     */
    public function testIsResetPasswordHashValid(
        $resetPasswordHash,
        $resetPasswordHashValidThrough,
        $sentHash,
        $isExpectedValid
    ) {
        $userData = new UserData();
        $userData->email = 'no-reply@shopsys.com';
        $billingAddressData = new BillingAddressData();
        $billingAddress = new BillingAddress($billingAddressData);
        $user = new User($userData, $billingAddress, null, null);

        $this->setProperty($user, 'resetPasswordHash', $resetPasswordHash);
        $this->setProperty($user, 'resetPasswordHashValidThrough', $resetPasswordHashValidThrough);

        $isResetPasswordHashValid = $user->isResetPasswordHashValid($sentHash);

        $this->assertSame($isExpectedValid, $isResetPasswordHashValid);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @param string $propertyName
     * @param mixed $value
     */
    private function setProperty(User $user, string $propertyName, $value)
    {
        $reflection = new ReflectionClass($user);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($user, $value);
    }

    public function testChangeEmailToExistingEmailButDifferentDomainDoNotThrowException()
    {
        $userData1 = new UserData();
        $userData1->domainId = 1;
        $userData1->email = 'no-reply@shopsys.com';
        $user1 = new User($userData1, $this->createBillingAddress(), null, null);

        $userData2 = new UserData();
        $userData2->domainId = 2;
        $userData2->email = 'no-reply2@shopsys.com';
        $billingAddress = $this->createBillingAddress();
        $user2 = new User($userData2, $billingAddress, null, $user1);

        $user2->changeEmail('no-reply@shopsys.com', $user1);

        $this->assertSame('no-reply@shopsys.com', $user2->getEmail());
    }

    public function testCreateNotDuplicateEmail()
    {
        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = new User(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'no-reply2@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $user2 = new User(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );

        $this->assertInstanceOf(User::class, $user2);
    }

    public function testCreateDuplicateEmail()
    {
        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = new User(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'no-reply@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);
        new User(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );
    }

    public function testCreateDuplicateEmailCaseInsentitive()
    {
        $billingAddress1 = $this->createBillingAddress();
        $deliveryAddress1 = $this->createDeliveryAddress();
        $userByEmail = null;
        $userData1 = new UserData();
        $userData1->firstName = 'firstName1';
        $userData1->lastName = 'lastName1';
        $userData1->email = 'no-reply@shopsys.com';
        $userData1->password = 'pa55w0rd';

        $user1 = new User(
            $userData1,
            $billingAddress1,
            $deliveryAddress1,
            $userByEmail
        );

        $billingAddress2 = $this->createBillingAddress();
        $deliveryAddress2 = $this->createDeliveryAddress();
        $userData2 = new UserData();
        $userData2->firstName = 'firstName2';
        $userData2->lastName = 'lastName2';
        $userData2->email = 'NO-reply@shopsys.com';
        $userData2->password = 'pa55w0rd';

        $this->expectException(\Shopsys\FrameworkBundle\Model\Customer\Exception\DuplicateEmailException::class);
        new User(
            $userData2,
            $billingAddress2,
            $deliveryAddress2,
            $user1
        );
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\BillingAddress
     */
    private function createBillingAddress()
    {
        return new BillingAddress(new BillingAddressData());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Customer\DeliveryAddress
     */
    private function createDeliveryAddress()
    {
        return new DeliveryAddress(new DeliveryAddressData());
    }
}
