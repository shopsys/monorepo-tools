<?php

namespace Tests\ShopBundle\Unit\Model\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;

class BasePriceCalculationTest extends PHPUnit_Framework_TestCase
{
    public function calculateBasePriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'basePriceWithoutVat' => '6998.78',
                'basePriceWithVat' => '8469',
                'basePriceVatAmount' => '1470.22',
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'basePriceWithoutVat' => '5784.8',
                'basePriceWithVat' => '7000',
                'basePriceVatAmount' => '1215.2',
            ],
        ];
    }

    /**
     * @dataProvider calculateBasePriceProvider
     */
    public function testCalculateBasePrice(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        $basePriceWithoutVat,
        $basePriceWithVat,
        $basePriceVatAmount
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

        $rounding = new Rounding($pricingSettingMock);
        $priceCalculation = new PriceCalculation($rounding);
        $basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

        $vat = new Vat(new VatData('vat', $vatPercent));

        $basePrice = $basePriceCalculation->calculateBasePrice($inputPrice, $inputPriceType, $vat);

        $this->assertSame(round($basePriceWithoutVat, 6), round($basePrice->getPriceWithoutVat(), 6));
        $this->assertSame(round($basePriceWithVat, 6), round($basePrice->getPriceWithVat(), 6));
        $this->assertSame(round($basePriceVatAmount, 6), round($basePrice->getVatAmount(), 6));
    }

    public function applyCoefficientProvider()
    {
        return [
            [
                'priceWithVat' => '100',
                'vatPercent' => '20',
                'coefficients' => ['2'],
                'resultPriceWithVat' => '200',
                'resultPriceWithoutVat' => '167',
                'resultVatAmount' => '33',
            ],
            [
                'priceWithVat' => '100',
                'vatPercent' => '10',
                'coefficients' => ['1'],
                'resultPriceWithVat' => '100',
                'resultPriceWithoutVat' => '91',
                'resultVatAmount' => '9',
            ],
            [
                'priceWithVat' => '100',
                'vatPercent' => '20',
                'coefficients' => ['0.6789'],
                'resultPriceWithVat' => '68',
                'resultPriceWithoutVat' => '57',
                'resultVatAmount' => '11',
            ],
        ];
    }

    /**
     * @dataProvider applyCoefficientProvider
     */
    public function testApplyCoefficient(
        $priceWithVat,
        $vatPercent,
        $coefficients,
        $resultPriceWithVat,
        $resultPriceWithoutVat,
        $resultVatAmount
    ) {
        $rounding = $this->getMock(
            Rounding::class,
            ['roundPriceWithVat', 'roundPriceWithoutVat', 'roundVatAmount'],
            [],
            '',
            false
        );
        $rounding->expects($this->any())->method('roundPriceWithVat')->willReturnCallback(function ($value) {
            return round($value);
        });
        $rounding->expects($this->any())->method('roundPriceWithoutVat')->willReturnCallback(function ($value) {
            return round($value);
        });
        $rounding->expects($this->any())->method('roundVatAmount')->willReturnCallback(function ($value) {
            return round($value);
        });
        $priceCalculation = new PriceCalculation($rounding);
        $basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

        $price = new Price(0, $priceWithVat);
        $vat = new Vat(new VatData('vat', $vatPercent));
        $resultPrice = $basePriceCalculation->applyCoefficients($price, $vat, $coefficients);

        $this->assertSame(round($resultPriceWithVat, 6), round($resultPrice->getPriceWithVat(), 6));
        $this->assertSame(round($resultPriceWithoutVat, 6), round($resultPrice->getPriceWithoutVat(), 6));
        $this->assertSame(round($resultVatAmount, 6), round($resultPrice->getVatAmount(), 6));
    }
}
