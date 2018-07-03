<?php

namespace Tests\FrameworkBundle\Unit\Model\Customer;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class CurrentCustomerTest extends TestCase
{
    public function testGetPricingGroupForUnregisteredCustomerReturnsDefaultPricingGroup()
    {
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $expectedPricingGroup = new PricingGroup($pricingGroupData, 1);

        $tokenStorageMock = $this->createMock(TokenStorage::class);
        $pricingGroupSettingFacadeMock = $this->getPricingGroupSettingFacadeMockReturningDefaultPricingGroup($expectedPricingGroup);

        $currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupSettingFacadeMock);

        $pricingGroup = $currentCustomer->getPricingGroup();
        $this->assertSame($expectedPricingGroup, $pricingGroup);
    }

    public function testGetPricingGroupForRegisteredCustomerReturnsHisPricingGroup()
    {
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $expectedPricingGroup = new PricingGroup($pricingGroupData, 1);
        $user = $this->getUserWithPricingGroup($expectedPricingGroup);

        $tokenStorageMock = $this->getTokenStorageMockForUser($user);
        $pricingGroupFacadeMock = $this->createMock(PricingGroupSettingFacade::class);

        $currentCustomer = new CurrentCustomer($tokenStorageMock, $pricingGroupFacadeMock);

        $pricingGroup = $currentCustomer->getPricingGroup();
        $this->assertSame($expectedPricingGroup, $pricingGroup);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $defaultPricingGroup
     * @return \PHPUnit\Framework\MockObject\MockObject|\Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade
     */
    private function getPricingGroupSettingFacadeMockReturningDefaultPricingGroup(PricingGroup $defaultPricingGroup)
    {
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
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @return \Shopsys\FrameworkBundle\Model\Customer\User
     */
    private function getUserWithPricingGroup(PricingGroup $pricingGroup)
    {
        $billingAddress = $this->createMock(BillingAddress::class);
        $userData = new UserData();
        $userData->pricingGroup = $pricingGroup;

        return new User($userData, $billingAddress, null);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User $user
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage
     */
    private function getTokenStorageMockForUser(User $user)
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->setMethods(['getUser'])
            ->getMockForAbstractClass();
        $tokenMock->method('getUser')->willReturn($user);

        $tokenStorageMock = $this->getMockBuilder(TokenStorage::class)
            ->setMethods(['getToken'])
            ->disableOriginalConstructor()
            ->getMock();
        $tokenStorageMock->method('getToken')->willReturn($tokenMock);

        return $tokenStorageMock;
    }
}
