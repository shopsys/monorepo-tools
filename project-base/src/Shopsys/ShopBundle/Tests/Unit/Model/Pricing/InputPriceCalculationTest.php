<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;

class InputPriceCalculationTest extends PHPUnit_Framework_TestCase {

    /**
     * @dataProvider getInputPriceDataProvider
     */
    public function testGetInputPrice($inputPriceType, $priceWithVat, $vatPercent, $expectedResult) {
        $inputPriceCalculation = new InputPriceCalculation();
        $actualInputPrice = $inputPriceCalculation->getInputPrice($inputPriceType, $priceWithVat, $vatPercent);

        $this->assertEquals(round($expectedResult, 6), round($actualInputPrice, 6));
    }

    public function getInputPriceDataProvider() {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'priceWithVat' => 121,
                'vatPercent' => 21,
                'expectedResult' => 100,
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'priceWithVat' => 121,
                'vatPercent' => 21,
                'expectedResult' => 121,
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'priceWithVat' => 100,
                'vatPercent' => 0,
                'expectedResult' => 100,
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'priceWithVat' => 100,
                'vatPercent' => 21,
                'expectedResult' => '82.644628',
            ],
        ];
    }

}
