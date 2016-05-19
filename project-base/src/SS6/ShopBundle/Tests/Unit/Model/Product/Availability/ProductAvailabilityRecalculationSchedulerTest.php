<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Availability;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

/**
 * @UglyTest
 */
class ProductAvailabilityRecalculationSchedulerTest extends PHPUnit_Framework_TestCase {

	public function testScheduleRecalculateAvailabilityForProduct() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
		$productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForProduct($productMock);
		$products = $productAvailabilityRecalculationScheduler->getProductsForImmediatelyRecalculation();

		$this->assertCount(1, $products);
		$this->assertSame($productMock, array_pop($products));
	}

	public function testCleanImmediatelyRecalculationSchedule() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productAvailabilityRecalculationScheduler = new ProductAvailabilityRecalculationScheduler($productRepositoryMock);
		$productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForProduct($productMock);
		$productAvailabilityRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
		$products = $productAvailabilityRecalculationScheduler->getProductsForImmediatelyRecalculation();

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
		$productAvailabilityRecalculationScheduler->scheduleRecalculateAvailabilityForAllProducts();

		$this->assertCount(0, $productAvailabilityRecalculationScheduler->getProductsForImmediatelyRecalculation());
		$this->assertSame($productsIterator, $productAvailabilityRecalculationScheduler->getProductsIteratorForRecalculation());
	}

}
