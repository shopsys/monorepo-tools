<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductRepository;

class ProductPriceRecalculationSchedulerTest extends PHPUnit_Framework_TestCase {

	public function testScheduleRecalculatePriceForProduct() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($productMock);
		$products = $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation();

		$this->assertCount(1, $products);
		$this->assertSame($productMock, array_pop($products));
	}

	public function testCleanImmediatelyRecalculationSchedule() {
		$productRepositoryMock = $this->getMock(ProductRepository::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleRecalculatePriceForProduct($productMock);
		$productPriceRecalculationScheduler->cleanImmediatelyRecalculationSchedule();
		$products = $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation();

		$this->assertCount(0, $products);
	}

	public function testScheduleRecalculatePriceForAllProducts() {
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$productsIterator = [$productMock];
		$productRepositoryMock = $this->getMock(
			ProductRepository::class,
			['markAllProductsForPriceRecalculation', 'getProductsForPriceRecalculationIterator'],
			[],
			'',
			false
		);
		$productRepositoryMock->expects($this->once())->method('markAllProductsForPriceRecalculation');
		$productRepositoryMock
			->expects($this->once())
			->method('getProductsForPriceRecalculationIterator')
			->willReturn($productsIterator);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleRecalculatePriceForAllProducts();

		$this->assertCount(0, $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation());
		$this->assertSame($productsIterator, $productPriceRecalculationScheduler->getProductsIteratorForRecalculation());
	}

	public function testScheduleRecalculatePriceForVat() {
		$vatMock = $this->getMock(Vat::class, null, [], '', false);
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$productsIterator = [$productMock];
		$productRepositoryMock = $this->getMock(
			ProductRepository::class,
			['markProductsForPriceRecalculationByVat', 'getProductsForPriceRecalculationIterator'],
			[],
			'',
			false
		);
		$productRepositoryMock
			->expects($this->once())
			->method('markProductsForPriceRecalculationByVat')
			->with($this->equalTo($vatMock));
		$productRepositoryMock
			->expects($this->once())
			->method('getProductsForPriceRecalculationIterator')
			->willReturn($productsIterator);

		$productPriceRecalculationScheduler = new ProductPriceRecalculationScheduler($productRepositoryMock);
		$productPriceRecalculationScheduler->scheduleRecalculatePriceForVat($vatMock);

		$this->assertCount(0, $productPriceRecalculationScheduler->getProductsForImmediatelyRecalculation());
		$this->assertSame($productsIterator, $productPriceRecalculationScheduler->getProductsIteratorForRecalculation());
	}
}
