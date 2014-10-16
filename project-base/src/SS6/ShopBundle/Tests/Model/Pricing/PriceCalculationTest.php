<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class PriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testApplyVatPercentProvider() {
		return array(
			array(
				'priceWithoutVat' => '0',
				'vatPercent' => '21',
				'expectedPriceWithVat' => '0',
			),
			array(
				'priceWithoutVat' => '100',
				'vatPercent' => '0',
				'expectedPriceWithVat' => '100',
			),
			array(
				'priceWithoutVat' => '100',
				'vatPercent' => '21',
				'expectedPriceWithVat' => '121',
			),
			array(
				'priceWithoutVat' => '100.9',
				'vatPercent' => '21.1',
				'expectedPriceWithVat' => '122.1899',
			),
		);
	}

	/**
	 * @dataProvider testApplyVatPercentProvider
	 */
	public function testApplyVatPercent(
		$priceWithoutVat,
		$vatPercent,
		$expectedPriceWithVat
	) {
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->setMethods(array('getRoundingType'))
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getRoundingType')
				->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

		$rounding = new Rounding($pricingSettingMock);
		$priceCalculation = new PriceCalculation($rounding);
		$vat = new Vat(new VatData('testVat', $vatPercent));

		$actualPriceWithVat = $priceCalculation->applyVatPercent($priceWithoutVat, $vat);

		$this->assertEquals(round($expectedPriceWithVat, 6), round($actualPriceWithVat, 6));
	}

	public function testGetVatAmountByPriceWithVatProvider() {
		return array(
			array(
				'priceWithVat' => '0',
				'vatPercent' => '10',
				'expectedVatAmount' => '0',
			),
			array(
				'priceWithoutVat' => '100',
				'vatPercent' => '0',
				'expectedPriceWithVat' => '0',
			),
			array(
				'priceWithoutVat' => '100',
				'vatPercent' => '21',
				'expectedPriceWithVat' => '17.36',
			),
		);
	}

	/**
	 * @dataProvider testGetVatAmountByPriceWithVatProvider
	 */
	public function testGetVatAmountByPriceWithVat(
		$priceWithVat,
		$vatPercent,
		$expectedVatAmount
	) {
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->setMethods(array('getRoundingType'))
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getRoundingType')
				->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

		$rounding = new Rounding($pricingSettingMock);
		$priceCalculation = new PriceCalculation($rounding);
		$vat = new Vat(new VatData('testVat', $vatPercent));
		
		$actualVatAmount = $priceCalculation->getVatAmountByPriceWithVat($priceWithVat, $vat);

		$this->assertEquals(round($expectedVatAmount, 6), round($actualVatAmount, 6));
	}

}
