<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product\Pricing;

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

    public function testRunImmediatelyRecalculations() {
        $productMock = $this->getMock(Product::class, null, [], '', false);
        $pricingGroupMock = $this->getMock(PricingGroup::class, null, [], '', false);
        $productServiceMock = $this->getMock(ProductService::class, null, [], '', false);

        $emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
        $entityManagerFacadeMock = $this->getMock(EntityManagerFacade::class, [], [], '', false);
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPrice = new ProductPrice(new Price(0, 0), false);
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($productPrice);
        $productCalculatedPriceRepositoryMock = $this->getMock(
            ProductCalculatedPriceRepository::class,
            ['saveCalculatedPrice'],
            [],
            '',
            false
        );
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock->scheduleProductForImmediateRecalculation($productMock);
        $pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getAll'], [], '', false);
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
