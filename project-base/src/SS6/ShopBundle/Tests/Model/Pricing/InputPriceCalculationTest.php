<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;

class InputPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testGetInputPriceWithVat() {
		$priceWithVat = '100';

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithVat = $inputPriceCalculation->getInputPriceWithVat($priceWithVat);

		$this->assertEquals($priceWithVat, $inputPriceWithVat);
	}

	public function testGetInputPriceWithoutVat() {
		$priceWithVat = '100';
		$vatPercent = 20;

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithoutVat = $inputPriceCalculation->getInputPriceWithoutVat($priceWithVat, $vatPercent);

		$this->assertEquals(round($priceWithVat * 100 / (100 + $vatPercent), 6), round($inputPriceWithoutVat, 6));
	}

}
