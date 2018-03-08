<?php

namespace Tests\ShopBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductPriceRecalculationSchedulerTest extends TestCase
{
    public function testProductCanBeScheduledForImmediateRecalculation()
    {
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
        $productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $products = $productPriceRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(1, $products);
        $this->assertSame($productMock, array_pop($products));
    }

    public function testImmediateRecalculationScheduleCanBeCleaned()
    {
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
        $productPriceRecalculationScheduler->scheduleProductForImmediateRecalculation($productMock);
        $productPriceRecalculationScheduler->cleanScheduleForImmediateRecalculation();
        $products = $productPriceRecalculationScheduler->getProductsForImmediateRecalculation();

        $this->assertCount(0, $products);
    }

    public function testAllProductsCanBeScheduledForDelayedRecalculation()
    {
        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productsIterator = [$productMock];
        $productRepositoryMock = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(['markAllProductsForPriceRecalculation', 'getProductsForPriceRecalculationIterator'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepositoryMock->expects($this->once())->method('markAllProductsForPriceRecalculation');
        $productRepositoryMock
            ->expects($this->once())
            ->method('getProductsForPriceRecalculationIterator')
            ->willReturn($productsIterator);

        $productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
        $productPriceRecalculationScheduler->scheduleAllProductsForDelayedRecalculation();

        $this->assertCount(0, $productPriceRecalculationScheduler->getProductsForImmediateRecalculation());
        $this->assertSame($productsIterator, $productPriceRecalculationScheduler->getProductsIteratorForDelayedRecalculation());
    }
}
