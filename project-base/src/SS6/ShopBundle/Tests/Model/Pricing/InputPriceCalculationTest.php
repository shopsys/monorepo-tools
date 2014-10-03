<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class InputPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testGetInputPriceWithVat() {
		$basePriceWithVat = '100';

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithVat = $inputPriceCalculation->getInputPriceWithVat($basePriceWithVat);

		$this->assertEquals($basePriceWithVat, $inputPriceWithVat);
	}

	public function testGetInputPriceWithoutVat() {
		$basePriceWithVat = '100';
		$vat = new Vat(new VatData('vatName', 20));

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithoutVat = $inputPriceCalculation->getInputPriceWithoutVat($basePriceWithVat, $vat);

		$this->assertEquals(round($basePriceWithVat * 100 / (100 + 20), 6), round($inputPriceWithoutVat, 6));
	}

}
