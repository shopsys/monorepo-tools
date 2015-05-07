<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Availability;

use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Availability\Availability;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityData;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityCalculation;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Product;

class ProductAvailabilityRecalculatorTest extends PHPUnit_Framework_TestCase {

	public function testRunImmediatelyRecalculations() {
		$productMock = $this->getMock(Product::class, null, [], '', false);

		$emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
		$productAvailabilityCalculationMock = $this->getMock(
			ProductAvailabilityCalculation::class,
			['getCalculatedAvailability'],
			[],
			'',
			false
		);
		$productAvailabilityCalculationMock
			->expects($this->once())
			->method('getCalculatedAvailability')
			->willReturn(new Availability(new AvailabilityData([], null)));
		$productAvailabilityRecalculationSchedulerMock = $this->getMock(
			ProductAvailabilityRecalculationScheduler::class,
			null,
			[],
			'',
			false
		);
		$productAvailabilityRecalculationSchedulerMock->scheduleRecalculateAvailabilityForProduct($productMock);

		$productAvailabilityRecalculator = new ProductAvailabilityRecalculator(
			$emMock,
			$productAvailabilityRecalculationSchedulerMock,
			$productAvailabilityCalculationMock
		);

		$productAvailabilityRecalculator->runImmediatelyRecalculations();
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

		$emMock = $this->getMock(EntityManager::class, ['clear', 'flush'], [], '', false);
		$productAvailabilityCalculationMock = $this->getMock(
			ProductAvailabilityCalculation::class,
			['getCalculatedAvailability'],
			[],
			'',
			false
		);
		$productAvailabilityCalculationMock
			->expects($this->exactly($calculationLimit))
			->method('getCalculatedAvailability')
			->willReturn(new Availability(new AvailabilityData([], null)));
		$productAvailabilityRecalculationSchedulerMock = $this->getMock(
			ProductAvailabilityRecalculationScheduler::class,
			['getProductsIteratorForRecalculation'],
			[],
			'',
			false
		);
		$productAvailabilityRecalculationSchedulerMock
			->expects($this->once())
			->method('getProductsIteratorForRecalculation')
			->willReturn($productIterator);

		$productAvailabilityRecalculator = new ProductAvailabilityRecalculator(
			$emMock,
			$productAvailabilityRecalculationSchedulerMock,
			$productAvailabilityCalculationMock
		);

		$calculationCallbackLimit = $calculationLimit;
		$recalculatedCount = $productAvailabilityRecalculator->runScheduledRecalculations(function () use (&$calculationCallbackLimit) {
			return $calculationCallbackLimit-- > 0;
		});

		$this->assertSame($calculationLimit, $recalculatedCount);
	}
}
