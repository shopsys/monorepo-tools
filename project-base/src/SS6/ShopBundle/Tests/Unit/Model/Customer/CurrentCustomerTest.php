<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Customer;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Customer\BillingAddress;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\User;
use SS6\ShopBundle\Model\Customer\UserData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurrentCustomerTest extends PHPUnit_Framework_TestCase {

	public function testGetPricingGroupForUnregisteredCustomerReturnsDefaultPricingGroup() {
		$expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);

		$tokenStorageMock = $this->getMock(TokenStorage::class, [], [], '', false);
		$pricingGroupSettingFacadeMock = $this->getPricingGroupSettingFacadeMockReturningDefaultPricingGroup($expectedPricingGroup);

		$currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupSettingFacadeMock);

		$pricingGroup = $currentCustomer->getPricingGroup();
		$this->assertSame($expectedPricingGroup, $pricingGroup);
	}

	public function testGetPricingGroupForRegisteredCustomerReturnsHisPricingGroup() {
		$expectedPricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);
		$user = $this->getUserWithPricingGroup($expectedPricingGroup);

		$tokenStorageMock = $this->getTokenStorageMockForUser($user);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupSettingFacade::class, [], [], '', false);

		$currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupFacadeMock);

		$pricingGroup = $currentCustomer->getPricingGroup();
		$this->assertSame($expectedPricingGroup, $pricingGroup);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $defaultPricingGroup
	 * @return \PHPUnit_Framework_MockObject_MockObject|\SS6\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade
	 */
	private function getPricingGroupSettingFacadeMockReturningDefaultPricingGroup(PricingGroup $defaultPricingGroup) {
		$pricingGroupSettingFacadeMock = $this->getMockBuilder(PricingGroupSettingFacade::class)
			->setMethods(['getDefaultPricingGroupByCurrentDomain'])
			->disableOriginalConstructor()
			->getMock();

		$pricingGroupSettingFacadeMock
			->method('getDefaultPricingGroupByCurrentDomain')
			->willReturn($defaultPricingGroup);

		return $pricingGroupSettingFacadeMock;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Pricing\Group\PricingGroup $pricingGroup
	 * @return \SS6\ShopBundle\Model\Customer\User
	 */
	private function getUserWithPricingGroup(PricingGroup $pricingGroup) {
		$billingAddress = $this->getMock(BillingAddress::class, [], [], '', false);
		$userData = new UserData();
		$userData->pricingGroup = $pricingGroup;

		return new User($userData, $billingAddress, null);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\User $user
	 * @return \PHPUnit_Framework_MockObject_MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
	 */
	private function getTokenStorageMockForUser(User $user) {
		$tokenMock = $this->getMockBuilder(TokenInterface::class)
			->setMethods(['getUser'])
			->getMockForAbstractClass();
		$tokenMock->method('getUser')->willReturn($user);

		$tokenStorageMock = $this->getMock(TokenStorage::class, ['getToken'], [], '', false);
		$tokenStorageMock->method('getToken')->willReturn($tokenMock);

		return $tokenStorageMock;
	}
}
