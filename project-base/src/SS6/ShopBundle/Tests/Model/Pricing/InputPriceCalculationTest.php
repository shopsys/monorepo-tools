<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;

class InputPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testGetInputPriceWithoutVat() {
		$priceWithVat = '100';
		$vatPercent = 20;

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithoutVat = $inputPriceCalculation->getInputPriceWithoutVat($priceWithVat, $vatPercent);

		$this->assertSame(round($priceWithVat * 100 / (100 + $vatPercent), 6), round($inputPriceWithoutVat, 6));
	}

}
