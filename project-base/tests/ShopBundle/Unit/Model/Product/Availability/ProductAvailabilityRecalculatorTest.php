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
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $emMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['clear', 'flush'])
            ->getMock();
        $entityManagerFacadeMock = $this->createMock(EntityManagerFacade::class);
        $productAvailabilityCalculationMock = $this->getMockBuilder(ProductAvailabilityCalculation::class)
            ->disableOriginalConstructor()
            ->setMethods(['calculateAvailability'])
            ->getMock();
        $productAvailabilityCalculationMock
            ->expects($this->once())
            ->method('calculateAvailability')
            ->willReturn(new Availability(new AvailabilityData([])));
        $productAvailabilityRecalculationSchedulerMock = $this->getMockBuilder(ProductAvailabilityRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productAvailabilityRecalculationSchedulerMock
            ->expects($this->once())
            ->method('getProductsForImmediateRecalculation')
            ->will($this->returnValue([$productMock]));

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
        $variantMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['isVariant', 'getMainVariant', 'setCalculatedAvailability'])
            ->getMock();
        $mainVariantMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCalculatedAvailability'])
            ->getMock();

        $variantMock->expects($this->once())->method('isVariant')->willReturn(true);
        $variantMock->expects($this->once())->method('getMainVariant')->willReturn($mainVariantMock);
        $mainVariantMock->expects($this->once())->method('setCalculatedAvailability');

        $emMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['flush'])
            ->getMock();
        $entityManagerFacadeMock = $this->createMock(EntityManagerFacade::class);
        $productAvailabilityRecalculationSchedulerMock = $this->getMockBuilder(ProductAvailabilityRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProductsForImmediateRecalculation'])
            ->getMock();
        $productAvailabilityRecalculationSchedulerMock
            ->expects($this->once())
            ->method('getProductsForImmediateRecalculation')
            ->willReturn([$variantMock]);
        $productAvailabilityCalculationMock = $this->getMockBuilder(ProductAvailabilityCalculation::class)
            ->disableOriginalConstructor()
            ->setMethods(['calculateAvailability'])
            ->getMock();
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
