<?php

namespace SS6\ShopBundle\Tests\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\PriceCalculation as GenericPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat;
use SS6\ShopBundle\Model\Product\PriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceProvider() {
		return array(
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
				'inputPrice' => '6999',
				'vatPercent' => '21',
				'basePriceWithoutVat' => '6998.78',
				'basePriceWithVat' => '8469',
			),
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
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
		$pricingCalculation = new GenericPriceCalculation();

		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->setMethods(array('getInputPriceType'))
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getInputPriceType')
				->will($this->returnValue($inputPriceType));
		$priceCalculation = new PriceCalculation($pricingCalculation, $pricingSettingMock);

		$vat = new Vat('vat', $vatPercent);

		$product = new Product(new ProductData(null, null, null, null, null, $inputPrice, $vat));

		$price = $priceCalculation->calculatePrice($product);

		$this->assertEquals(round($basePriceWithoutVat, 6), round($price->getBasePriceWithoutVat(), 6));
		$this->assertEquals(round($basePriceWithVat, 6), round($price->getBasePriceWithVat(), 6));
	}

}
