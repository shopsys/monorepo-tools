<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product\Availability;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class ProductAvailabilityRecalculationSchedulerTest extends PHPUnit_Framework_TestCase
{

    public function testScheduleRecalculateAvailabilityForProduct() {
        $productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
        $productMock = $this->getMock(Product::class, null, [], '', false);

        $productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
        $productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $products = $productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(1, $products);
        $this->assertSame($productMock, array_pop($products));
    }

    public function testCleanImmediatelyRecalculationSchedule() {
        $productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
        $productMock = $this->getMock(Product::class, null, [], '', false);

        $productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
        $productAvailabilityRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $productAvailabilityRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $products = $productAvailabilityRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(0, $products);
    }

    public function testScheduleRecalculateAvailabilityForAllProducts() {
        $productMock = $this->getMock(Product::class, null, [], '', false);
        $productsIterator = [$productMock];
        $productRepositoryMock = $this->getMock(
            ProductRepository::class,
            ['markAllProductsForAvailabilityRecalculation', 'getProductsForAvailabilityRecalculationIterator'],
            [],
            '',
            false
        );
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
