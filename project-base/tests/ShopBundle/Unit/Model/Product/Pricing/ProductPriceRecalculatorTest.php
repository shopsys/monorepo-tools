<?php

namespace Tests\ShopBundle\Unit\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductService;

class ProductPriceRecalculatorTest extends PHPUnit_Framework_TestCase
{
    public function testRunImmediatelyRecalculations()
    {
        $productMock = $this->getMockBuilder(Product::class)->setMethods(null)->disableOriginalConstructor()->getMock();
        $pricingGroupMock = $this->getMockBuilder(PricingGroup::class)->setMethods(null)->disableOriginalConstructor()->getMock();
        $productServiceMock = $this->getMockBuilder(ProductService::class)->setMethods(null)->disableOriginalConstructor()->getMock();
        $emMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['clear', 'flush'])
            ->getMock();
        $entityManagerFacadeMock = $this->createMock(EntityManagerFacade::class);
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->disableOriginalConstructor()
            ->setMethods(['calculatePrice'])
            ->getMock();
        $productPrice = new ProductPrice(new Price(0, 0), false);
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($productPrice);
        $productCalculatedPriceRepositoryMock = $this->getMockBuilder(ProductCalculatedPriceRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['saveCalculatedPrice'])
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProductsForImmediateRecalculation'])
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('getProductsForImmediateRecalculation')->will($this->returnValue([$productMock]));
        $pricingGroupFacadeMock = $this->getMockBuilder(PricingGroupFacade::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAll'])
            ->getMock();
        $pricingGroupFacadeMock->expects($this->once())->method('getAll')->willReturn([$pricingGroupMock]);

        $productPriceRecalculator = new ProductPriceRecalculator(
            $emMock,
            $entityManagerFacadeMock,
            $productPriceCalculationMock,
            $productCalculatedPriceRepositoryMock,
            $productPriceRecalculationSchedulerMock,
            $pricingGroupFacadeMock,
            $productServiceMock
        );

        $productPriceRecalculator->runImmediateRecalculations();
    }
}
