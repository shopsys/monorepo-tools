<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Pricing;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupFacade;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Pricing\ProductCalculatedPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceRecalculator;
use SS6\ShopBundle\Model\Product\Product;

class ProductPriceRecalculatorTest extends PHPUnit_Framework_TestCase {

	public function testRunImmediatelyRecalculations() {
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$pricingGroupMock = $this->getMock(PricingGroup::class, null, [], '', false);

		$emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
		$productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn(new Price(0, 0, 0));
		$productCalculatedPriceRepositoryMock = $this->getMock(
			ProductCalculatedPriceRepository::class,
			['saveCalculatedPrice'],
			[],
			'',
			false
		);
		$productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);
		$productPriceRecalculationSchedulerMock->scheduleRecalculatePriceForProduct($productMock);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getAll'], [], '', false);
		$pricingGroupFacadeMock->expects($this->once())->method('getAll')->willReturn([$pricingGroupMock]);

		$productPriceRecalculator = new ProductPriceRecalculator(
			$emMock,
			$productPriceCalculationMock,
			$productCalculatedPriceRepositoryMock,
			$productPriceRecalculationSchedulerMock,
			$pricingGroupFacadeMock
		);

		$productPriceRecalculator->runImmediatelyRecalculations();
	}

	public function testRunScheduledRecalculations() {
		$calculationLimit = 3;
		$productMock = $this->getMock(Product::class, null, [], '', false);
		$productIterator = [
			[$productMock],
			[$productMock],
			[$productMock],
			[$productMock],
		];

		$pricingGroupMock = $this->getMock(PricingGroup::class, null, [], '', false);

		$emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
		$productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
		$productPriceCalculationMock
			->expects($this->exactly($calculationLimit))
			->method('calculatePrice')
			->willReturn(new Price(0, 0, 0));
		$productCalculatedPriceRepositoryMock = $this->getMock(
			ProductCalculatedPriceRepository::class,
			['saveCalculatedPrice'],
			[],
			'',
			false
		);
		$productPriceRecalculationSchedulerMock = $this->getMock(
			ProductPriceRecalculationScheduler::class,
			['getProductsIteratorForRecalculation'],
			[],
			'',
			false
		);
		$productPriceRecalculationSchedulerMock
			->expects($this->once())
			->method('getProductsIteratorForRecalculation')
			->willReturn($productIterator);
		$pricingGroupFacadeMock = $this->getMock(PricingGroupFacade::class, ['getAll'], [], '', false);
		$pricingGroupFacadeMock->expects($this->once())->method('getAll')->willReturn([$pricingGroupMock]);

		$productPriceRecalculator = new ProductPriceRecalculator(
			$emMock,
			$productPriceCalculationMock,
			$productCalculatedPriceRepositoryMock,
			$productPriceRecalculationSchedulerMock,
			$pricingGroupFacadeMock
		);

		$calculationCallbackLimit = $calculationLimit;
		$recalculatedCount = $productPriceRecalculator->runScheduledRecalculations(function () use (&$calculationCallbackLimit) {
			return $calculationCallbackLimit-- > 0;
		});

		$this->assertSame($calculationLimit, $recalculatedCount);
	}
}
