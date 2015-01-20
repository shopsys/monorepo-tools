<?php

namespace SS6\ShopBundle\Tests\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;

class CurrentCustomerTest extends PHPUnit_Framework_TestCase {

	public function testGetPricingGroupNotLogged() {
		$expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);

		$securityContextMock = $this->getMock(SecurityContext::class, [], [], '', false);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getDefaultPricingGroupByCurrentDomain'], [], '', false);
		$pricingGroupFacadeMock
			->expects($this->once())
			->method('getDefaultPricingGroupByCurrentDomain')
			->willReturn($expectedPricingGroup);

		$currentCustomer = new CurrentCustomer($securityContextMock, $pricingGroupFacadeMock);

		$pricingGroup = $currentCustomer->getPricingGroup();
		$this->assertEquals($expectedPricingGroup, $pricingGroup);
	}

	public function testGetPricingGroup() {
		$expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);
		$billingAddress = $this->getMock(BillingAddress::class, [], [], '', false);
		$userData = new UserData();
		$userData->pricingGroup = $expectedPricingGroup;
		$user = new User($userData, $billingAddress, null);

		$tokenMock = $this->getMockBuilder(TokenInterface::class)
			->setMethods(['getUser'])
			->getMockForAbstractClass();
		$tokenMock->expects($this->once())->method('getUser')->willReturn($user);

		$securityContextMock = $this->getMock(SecurityContext::class, ['getToken'], [], '', false);
		$securityContextMock->expects($this->once())->method('getToken')->willReturn($tokenMock);

		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, [], [], '', false);

		$currentCustomer = new CurrentCustomer($securityContextMock, $pricingGroupFacadeMock);

		$pricingGroup = $currentCustomer->getPricingGroup();
		$this->assertEquals($expectedPricingGroup, $pricingGroup);
	}

}
