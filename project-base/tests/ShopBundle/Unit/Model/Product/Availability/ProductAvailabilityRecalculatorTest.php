<?php

namespace Tests\ShopBundle\Unit\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade;
use Shopsys\ShopBundle\Model\Product\Availability\Availability;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityData;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\ShopBundle\Model\Product\Product;

class ProductAvailabilityRecalculatorTest extends PHPUnit_Framework_TestCase
{
    public function testRunImmediatelyRecalculations()
    {
        $productMock = $this->getMock(Product::class, null, [], '', false);

        $emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
        $entityManagerFacadeMock = $this->getMock(EntityManagerFacade::class, [], [], '', false);
        $productAvailabilityCalculationMock = $this->getMock(
            ProductAvailabilityCalculation::class,
            ['calculateAvailability'],
            [],
            '',
            false
        );
        $productAvailabilityCalculationMock
            ->expects($this->once())
            ->method('calculateAvailability')
            ->willReturn(new Availability(new AvailabilityData([])));
        $productAvailabilityRecalculationSchedulerMock = $this->getMock(
            ProductAvailabilityRecalculationScheduler::class,
            null,
            [],
            '',
            false
        );
        $productAvailabilityRecalculationSchedulerMock->scheduleProductForImmediateRecalculation($productMock);

        $productAvailabilityRecalculator = new ProductAvailabilityRecalculator(
            $emMock,
            $entityManagerFacadeMock,
            $productAvailabilityRecalculationSchedulerMock,
            $productAvailabilityCalculationMock
        );

        $productAvailabilityRecalculator->runImmediateRecalculations();
    }

    public function testRecalculateAvailabilityForVariant()
    {
        $variantMock = $this->getMock(Product::class, ['isVariant', 'getMainVariant', 'setCalculatedAvailability'], [], '', false);
        $mainVariantMock = $this->getMock(Product::class, ['setCalculatedAvailability'], [], '', false);
        $variantMock
            ->expects($this->once())
            ->method('isVariant')
            ->willReturn(true);
        $variantMock
            ->expects($this->once())
            ->method('getMainVariant')
            ->willReturn($mainVariantMock);
        $mainVariantMock
            ->expects($this->once())
            ->method('setCalculatedAvailability');

        $emMock = $this->getMock(EntityManager::class, ['flush'], [], '', false);
        $entityManagerFacadeMock = $this->getMock(EntityManagerFacade::class, [], [], '', false);
        $productAvailabilityRecalculationSchedulerMock = $this->getMock(
            ProductAvailabilityRecalculationScheduler::class,
            ['getProductsForImmediateRecalculation'],
            [],
            '',
            false
        );
        $productAvailabilityRecalculationSchedulerMock
            ->expects($this->once())
            ->method('getProductsForImmediateRecalculation')
            ->willReturn([$variantMock]);
        $productAvailabilityCalculationMock = $this->getMock(
            ProductAvailabilityCalculation::class,
            ['calculateAvailability'],
            [],
            '',
            false
        );
        $productAvailabilityCalculationMock
            ->expects($this->exactly(2))
            ->method('calculateAvailability')
            ->willReturn(new Availability(new AvailabilityData([])));

        $productAvailabilityRecalculator = new ProductAvailabilityRecalculator(
            $emMock,
            $entityManagerFacadeMock,
            $productAvailabilityRecalculationSchedulerMock,
            $productAvailabilityCalculationMock
        );

        $productAvailabilityRecalculator->runImmediateRecalculations();
    }
}
