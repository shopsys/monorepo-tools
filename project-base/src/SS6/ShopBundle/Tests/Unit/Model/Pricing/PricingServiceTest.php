<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Pricing\PricingService;

class PricingServiceTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getMinimumPriceProvider
	 */
	public function testGetMinimumPrice(array $prices, $minimumPrice) {
		$pricingService = new PricingService();

		$this->assertEquals($minimumPrice, $pricingService->getMinimumPrice($prices));
	}

	public function getMinimumPriceProvider() {
		return [
			[
				'prices' => [
					new Price(20, 30, 10),
					new Price(10, 15, 5),
					new Price(100, 120, 20),
				],
				'minimumPrice' => new Price(10, 15, 5),
			],
			[
				'prices' => [
					new Price(10, 15, 5),
				],
				'minimumPrice' => new Price(10, 15, 5),
			],
			[
				'prices' => [
					new Price(10, 15, 5),
					new Price(10, 15, 5),
				],
				'minimumPrice' => new Price(10, 15, 5),
			],
		];
	}

	public function testGetMinimumPriceEmptyArray() {
		$pricingService = new PricingService();

		$this->setExpectedException(\SS6\ShopBundle\Model\Pricing\Exception\InvalidArgumentException::class);
		$pricingService->getMinimumPrice([]);
	}

	/**
	 * @dataProvider getAreDifferentProvider
	 */
	public function testAreDifferent(array $prices, $areDifferent) {
		$pricingService = new PricingService();

		$this->assertSame($areDifferent, $pricingService->areDifferent($prices));
	}

	public function getAreDifferentProvider() {
		return [
			[
				'prices' => [
					new Price(100, 120, 20),
					new Price(100, 120, 20),
				],
				'areDifferent' => false,
			],
			[
				'prices' => [
					new Price(100, 120, 20),
				],
				'areDifferent' => false,
			],
			[
				'prices' => [
					new Price(100, 120, 20),
					new Price('100', '120', '20'),
				],
				'areDifferent' => true,
			],
			[
				'prices' => [
					new Price(200, 240, 40),
					new Price(100, 120, 20),
				],
				'areDifferent' => true,
			],
		];
	}

	public function testAreDifferentEmptyArray() {
		$pricingService = new PricingService();

		$this->setExpectedException(\SS6\ShopBundle\Model\Pricing\Exception\InvalidArgumentException::class);
		$pricingService->areDifferent([]);
	}

}
