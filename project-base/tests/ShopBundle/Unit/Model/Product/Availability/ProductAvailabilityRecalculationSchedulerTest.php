<?php

namespace Tests\ShopBundle\Unit\Model\Product\Availability;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductAvailabilityRecalculationSchedulerTest extends TestCase
{
    public function testScheduleRecalculateAvailabilityForProduct()
    {
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
        $productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $products = $productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(1, $products);
        $this->assertSame($productMock, array_pop($products));
    }

    public function testCleanImmediatelyRecalculationSchedule()
    {
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
        $productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $products = $productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(0, $products);
    }

    public function testScheduleRecalculateAvailabilityForAllProducts()
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productsIterator = [$productMock];
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(['markAllProductsForAvailabilityRecalculation', 'getProductsForAvailabilityRecalculationIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepositoryMock->expects($this->once())->method('markAllProductsForAvailabilityRecalculation');
        $productRepositoryMock
            ->expects($this->once())
            ->method('getProductsForAvailabilityRecalculationIterator')
            ->willReturn($productsIterator);

        $productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
        $productAvailabilityRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

        $this->assertCount(0, $productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation());
        $this->assertSame($productsIterator, $productAvailabilityRecalculationScheduler->getProductsIteratorForDelayedRecalculation());
    }
}
