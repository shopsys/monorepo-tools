<?php

namespace Tests\ShopBundle\Unit\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Customer\BillingAddress;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\User;
use Shopsys\ShopBundle\Model\Customer\UserData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductPriceCalculationForUserTest extends PHPUnit_Framework_TestCase
{
    public function testCalculatePriceByUserAndDomainIdWithUser()
    {
        $product = $this->getMock(Product::class, [], [], '', false);
        $pricingGroup = new PricingGroup(new PricingGroupData('name', 1), 1);
        $billingAddress = $this->getMock(BillingAddress::class, [], [], '', false);
        $userData = new UserData();
        $userData->pricingGroup = $pricingGroup;
        $user = new User($userData, $billingAddress, null);
        $expectedProductPrice = new ProductPrice(new Price(1, 1), false);

        $currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);
        $pricingGroupSettingFacadeMock = $this->getMock(PricingGroupSettingFacade::class, [], [], '', false);

        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->getMock(Domain::class, [], [], '', false);

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
        $product = $this->getMock(Product::class, [], [], '', false);
        $pricingGroup = new PricingGroup(new PricingGroupData('name', 1), $domainId);
        $expectedProductPrice = new ProductPrice(new Price(1, 1), false);

        $currentCustomerMock = $this->getMock(CurrentCustomer::class, [], [], '', false);

        $pricingGroupFacadeMock = $this->getMock(PricingGroupSettingFacade::class, ['getDefaultPricingGroupByDomainId'], [], '', false);
        $pricingGroupFacadeMock
            ->expects($this->once())
            ->method('getDefaultPricingGroupByDomainId')
            ->with($this->equalTo($domainId))
            ->willReturn($pricingGroup);

        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->getMock(Domain::class, [], [], '', false);

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
