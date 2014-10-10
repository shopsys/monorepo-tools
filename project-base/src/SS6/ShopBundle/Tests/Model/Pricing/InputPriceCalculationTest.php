<?php

namespace SS6\ShopBundle\Tests\Model\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\InputPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;

class InputPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testGetInputPriceWithVat() {
		$priceWithVat = '100';

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithVat = $inputPriceCalculation->getInputPriceWithVat($priceWithVat);

		$this->assertEquals($priceWithVat, $inputPriceWithVat);
	}

	public function testGetInputPriceWithoutVat() {
		$priceWithVat = '100';
		$vat = new Vat(new VatData('vatName', 20));

		$inputPriceCalculation = new InputPriceCalculation();
		$inputPriceWithoutVat = $inputPriceCalculation->getInputPriceWithoutVat($priceWithVat, $vat);

		$this->assertEquals(round($priceWithVat * 100 / (100 + 20), 6), round($inputPriceWithoutVat, 6));
	}

}
