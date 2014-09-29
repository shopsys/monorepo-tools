<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Rounding;

class RoundingTest extends PHPUnit_Framework_TestCase {

	public function testRoundingProvider() {
		return array(
			array(
				'unroundedPrice' => '0',
				'expectedAsPriceWithVat' => '0',
				'expectedAsPriceWithoutVat' => '0',
				'expectedAsVatAmount' => '0',
			),
			array(
				'unroundedPrice' => '1',
				'expectedAsPriceWithVat' => '1',
				'expectedAsPriceWithoutVat' => '1',
				'expectedAsVatAmount' => '1',
			),
			array(
				'unroundedPrice' => '0.999',
				'expectedAsPriceWithVat' => '1',
				'expectedAsPriceWithoutVat' => '1',
				'expectedAsVatAmount' => '1',
			),
			array(
				'unroundedPrice' => '0.99',
				'expectedAsPriceWithVat' => '1',
				'expectedAsPriceWithoutVat' => '0.99',
				'expectedAsVatAmount' => '0.99',
			),
			array(
				'unroundedPrice' => '0.5',
				'expectedAsPriceWithVat' => '1',
				'expectedAsPriceWithoutVat' => '0.50',
				'expectedAsVatAmount' => '0.50',
			),
			array(
				'unroundedPrice' => '0.49',
				'expectedAsPriceWithVat' => '0',
				'expectedAsPriceWithoutVat' => '0.49',
				'expectedAsVatAmount' => '0.49',
			),
		);
	}

	/**
	 * @dataProvider testRoundingProvider
	 */
	public function testRounding(
		$unroundedPrice,
		$expectedAsPriceWithVat,
		$expectedAsPriceWithoutVat,
		$expectedAsVatAmount
	) {
		$rounding = new Rounding();

		$this->assertEquals(round($expectedAsPriceWithVat, 6), round($rounding->roundPriceWithVat($unroundedPrice), 6));
		$this->assertEquals(round($expectedAsPriceWithoutVat, 6), round($rounding->roundPriceWithoutVat($unroundedPrice), 6));
		$this->assertEquals(round($expectedAsVatAmount, 6), round($rounding->roundVatAmount($unroundedPrice), 6));
	}

}
