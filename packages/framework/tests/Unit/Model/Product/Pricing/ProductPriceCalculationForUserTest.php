<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Customer\BillingAddress;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomer;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductPriceCalculationForUserTest extends TestCase
{
    public function testCalculatePriceByUserAndDomainIdWithUser()
    {
        $product = $this->createMock(Product::class);
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $pricingGroup = new PricingGroup($pricingGroupData, 1);
        $billingAddress = $this->createMock(BillingAddress::class);
        $userData = new UserData();
        $userData->pricingGroup = $pricingGroup;
        $user = new User($userData, $billingAddress, null);
        $expectedProductPrice = new ProductPrice(new Price(1, 1), false);

        $currentCustomerMock = $this->createMock(CurrentCustomer::class);
        $pricingGroupSettingFacadeMock = $this->createMock(PricingGroupSettingFacade::class);

        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->createMock(Domain::class);

        $productPriceCalculationForUser = new ProductPriceCalculationForUser(
            $productPriceCalculationMock,
            $currentCustomerMock,
            $pricingGroupSettingFacadeMock,
            $domainMock
        );

        $productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, 1, $user);
        $this->assertSame($expectedProductPrice, $productPrice);
    }

    public function testCalculatePriceByUserAndDomainIdWithoutUser()
    {
        $domainId = 1;
        $product = $this->createMock(Product::class);
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroupData->coefficient = 1;
        $pricingGroup = new PricingGroup($pricingGroupData, $domainId);
        $expectedProductPrice = new ProductPrice(new Price(1, 1), false);

        $currentCustomerMock = $this->createMock(CurrentCustomer::class);

        $pricingGroupFacadeMock = $this->getMockBuilder(PricingGroupSettingFacade::class)
            ->setMethods(['getDefaultPricingGroupByDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupFacadeMock
            ->expects($this->once())
            ->method('getDefaultPricingGroupByDomainId')
            ->with($this->equalTo($domainId))
            ->willReturn($pricingGroup);

        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->createMock(Domain::class);

        $productPriceCalculationForUser = new ProductPriceCalculationForUser(
            $productPriceCalculationMock,
            $currentCustomerMock,
            $pricingGroupFacadeMock,
            $domainMock
        );

        $productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, $domainId, null);
        $this->assertSame($expectedProductPrice, $productPrice);
    }
}
