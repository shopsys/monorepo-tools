<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;

class InputPriceCalculationTest extends TestCase
{
    /**
     * @dataProvider getInputPriceDataProvider
     * @param mixed $inputPriceType
     * @param mixed $priceWithVat
     * @param mixed $vatPercent
     * @param mixed $expectedResult
     */
    public function testGetInputPrice($inputPriceType, $priceWithVat, $vatPercent, $expectedResult)
    {
        $inputPriceCalculation = new InputPriceCalculation();
        $actualInputPrice = $inputPriceCalculation->getInputPrice($inputPriceType, $priceWithVat, $vatPercent);

        $this->assertEquals(round($expectedResult, 6), round($actualInputPrice, 6));
    }

    public function getInputPriceDataProvider()
    {
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
