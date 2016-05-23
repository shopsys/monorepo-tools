<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\BillingAddressData;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;

class UserTest extends PHPUnit_Framework_TestCase {

	public function testGetFullNameReturnsLastnameAndFirstnameForUser() {
		$userData = new UserData(1, 'Firstname', 'Lastname');
		$billingAddressData = new BillingAddressData();
		$billingAddress = new BillingAddress($billingAddressData);
		$user = new User($userData, $billingAddress);

		$this->assertSame('Lastname Firstname', $user->getFullName());
	}

	public function testGetFullNameReturnsCompanyNameForCompanyUser() {
		$userData = new UserData(1, 'Firstname', 'Lastname');
		$billingAddressData = new BillingAddressData();
		$billingAddressData->companyCustomer = true;
		$billingAddressData->companyName = 'CompanyName';
		$billingAddress = new BillingAddress($billingAddressData);
		$user = new User($userData, $billingAddress);

		$this->assertSame('CompanyName', $user->getFullName());
	}
}
