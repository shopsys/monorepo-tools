<?php

namespace SS6\ShopBundle\Tests\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Vat;
use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Setting\Setting3;

class ProductCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceProvider() {
		return array(
			array(
				'inputPriceType' => Setting3::INPUT_PRICE_TYPE_WITHOUT_VAT,
				'inputPrice' => '6999',
				'vatPercent' => '21',
				'basePriceWithoutVat' => '6998.78',
				'basePriceWithVat' => '8469',
			),
			array(
				'inputPriceType' => Setting3::INPUT_PRICE_TYPE_WITH_VAT,
				'inputPrice' => '6999.99',
				'vatPercent' => '21',
				'basePriceWithoutVat' => '5784.8',
				'basePriceWithVat' => '7000',
			),
		);
	}

	/**
	 * @dataProvider testCalculatePriceProvider
	 */
	public function testCalculatePrice(
		$inputPriceType,
		$inputPrice,
		$vatPercent,
		$basePriceWithoutVat,
		$basePriceWithVat
	) {
		$settingMock = $this->getMockBuilder(Setting3::class)
			->setMethods(array('get'))
			->disableOriginalConstructor()
			->getMock();
		$settingMock->expects($this->any())->method('get')->will($this->returnValue($inputPriceType));
		$priceCalculation = new PriceCalculation($settingMock);

		$vat = new Vat('vat', $vatPercent);

		$product = new Product(new ProductData(null, null, null, null, null, $inputPrice, $vat));

		$price = $priceCalculation->calculatePrice($product);

		$this->assertEquals(round($basePriceWithoutVat, 6), round($price->getBasePriceWithoutVat(), 6));
		$this->assertEquals(round($basePriceWithVat, 6), round($price->getBasePriceWithVat(), 6));
	}

}
