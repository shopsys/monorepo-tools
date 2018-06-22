<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use PHPUnit\Framework\TestCase;
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
}
