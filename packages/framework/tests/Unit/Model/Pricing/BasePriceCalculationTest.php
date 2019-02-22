<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class BasePriceCalculationTest extends TestCase
{
    public function calculateBasePriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => Money::fromString('6999'),
                'vatPercent' => '21',
                'basePriceWithoutVat' => Money::fromString('6998.78'),
                'basePriceWithVat' => Money::fromString('8469'),
                'basePriceVatAmount' => Money::fromString('1470.22'),
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => Money::fromString('6999.99'),
                'vatPercent' => '21',
                'basePriceWithoutVat' => Money::fromString('5784.8'),
                'basePriceWithVat' => Money::fromString('7000'),
                'basePriceVatAmount' => Money::fromString('1215.2'),
            ],
        ];
    }

    /**
     * @dataProvider calculateBasePriceProvider
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $inputPrice
     * @param mixed $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $basePriceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $basePriceWithVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $basePriceVatAmount
     */
    public function testCalculateBasePrice(
        int $inputPriceType,
        Money $inputPrice,
        $vatPercent,
        Money $basePriceWithoutVat,
        Money $basePriceWithVat,
        Money $basePriceVatAmount
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

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);

        $basePrice = $basePriceCalculation->calculateBasePrice($inputPrice, $inputPriceType, $vat);

        $this->assertThat($basePrice->getPriceWithoutVat(), new IsMoneyEqual($basePriceWithoutVat));
        $this->assertThat($basePrice->getPriceWithVat(), new IsMoneyEqual($basePriceWithVat));
        $this->assertThat($basePrice->getVatAmount(), new IsMoneyEqual($basePriceVatAmount));
    }

    public function applyCoefficientProvider()
    {
        return [
            [
                'priceWithVat' => Money::fromInteger(100),
                'vatPercent' => '20',
                'coefficients' => ['2'],
                'resultPriceWithVat' => Money::fromInteger(200),
                'resultPriceWithoutVat' => Money::fromInteger(167),
                'resultVatAmount' => Money::fromInteger(33),
            ],
            [
                'priceWithVat' => Money::fromInteger(100),
                'vatPercent' => '10',
                'coefficients' => ['1'],
                'resultPriceWithVat' => Money::fromInteger(100),
                'resultPriceWithoutVat' => Money::fromInteger(91),
                'resultVatAmount' => Money::fromInteger(9),
            ],
            [
                'priceWithVat' => Money::fromInteger(100),
                'vatPercent' => '20',
                'coefficients' => ['0.6789'],
                'resultPriceWithVat' => Money::fromInteger(68),
                'resultPriceWithoutVat' => Money::fromInteger(57),
                'resultVatAmount' => Money::fromInteger(11),
            ],
        ];
    }

    /**
     * @dataProvider applyCoefficientProvider
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @param mixed $vatPercent
     * @param mixed $coefficients
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $resultPriceWithVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $resultPriceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $resultVatAmount
     */
    public function testApplyCoefficient(
        Money $priceWithVat,
        $vatPercent,
        $coefficients,
        Money $resultPriceWithVat,
        Money $resultPriceWithoutVat,
        Money $resultVatAmount
    ) {
        $rounding = $this->getMockBuilder(Rounding::class)
            ->setMethods(['roundPriceWithVat', 'roundPriceWithoutVat', 'roundVatAmount'])
            ->disableOriginalConstructor()
            ->getMock();
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

        $price = new Price(Money::zero(), $priceWithVat);
        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $resultPrice = $basePriceCalculation->applyCoefficients($price, $vat, $coefficients);

        $this->assertThat($resultPrice->getPriceWithVat(), new IsMoneyEqual($resultPriceWithVat));
        $this->assertThat($resultPrice->getPriceWithoutVat(), new IsMoneyEqual($resultPriceWithoutVat));
        $this->assertThat($resultPrice->getVatAmount(), new IsMoneyEqual($resultVatAmount));
    }
}
