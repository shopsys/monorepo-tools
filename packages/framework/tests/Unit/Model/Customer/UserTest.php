<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use DateTime;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddressData;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;

class UserTest extends TestCase
{
    public function testGetFullNameReturnsLastnameAndFirstnameForUser()
    {
        $userData = new UserData();
        $userData->firstName = 'Firstname';
        $userData->lastName = 'Lastname';
        $billingAddressData = new BillingAddressData();
        $billingAddress = new BillingAddress($billingAddressData);
        $user = new User($userData, $billingAddress);

        $this->assertSame('Lastname Firstname', $user->getFullName());
    }

    public function testGetFullNameReturnsCompanyNameForCompanyUser()
    {
        $userData = new UserData();
        $userData->firstName = 'Firstname';
        $userData->lastName = 'Lastname';
        $billingAddressData = new BillingAddressData();
        $billingAddressData->companyCustomer = true;
        $billingAddressData->companyName = 'CompanyName';
        $billingAddress = new BillingAddress($billingAddressData);
        $user = new User($userData, $billingAddress);

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
        $billingAddressData = new BillingAddressData();
        $billingAddress = new BillingAddress($billingAddressData);
        $user = new User($userData, $billingAddress);

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
}
