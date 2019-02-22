<?php

namespace Tests\FrameworkBundle\Unit\Model\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class RoundingTest extends TestCase
{
    public function roundingProvider()
    {
        return [
            [
                'unroundedPrice' => Money::fromString('0'),
                'expectedAsPriceWithVat' => Money::fromString('0'),
                'expectedAsPriceWithoutVat' => Money::fromString('0'),
                'expectedAsVatAmount' => Money::fromString('0'),
            ],
            [
                'unroundedPrice' => Money::fromString('1'),
                'expectedAsPriceWithVat' => Money::fromString('1'),
                'expectedAsPriceWithoutVat' => Money::fromString('1'),
                'expectedAsVatAmount' => Money::fromString('1'),
            ],
            [
                'unroundedPrice' => Money::fromString('0.999'),
                'expectedAsPriceWithVat' => Money::fromString('1'),
                'expectedAsPriceWithoutVat' => Money::fromString('1'),
                'expectedAsVatAmount' => Money::fromString('1'),
            ],
            [
                'unroundedPrice' => Money::fromString('0.99'),
                'expectedAsPriceWithVat' => Money::fromString('1'),
                'expectedAsPriceWithoutVat' => Money::fromString('0.99'),
                'expectedAsVatAmount' => Money::fromString('0.99'),
            ],
            [
                'unroundedPrice' => Money::fromString('0.5'),
                'expectedAsPriceWithVat' => Money::fromString('1'),
                'expectedAsPriceWithoutVat' => Money::fromString('0.50'),
                'expectedAsVatAmount' => Money::fromString('0.50'),
            ],
            [
                'unroundedPrice' => Money::fromString('0.49'),
                'expectedAsPriceWithVat' => Money::fromString('0'),
                'expectedAsPriceWithoutVat' => Money::fromString('0.49'),
                'expectedAsVatAmount' => Money::fromString('0.49'),
            ],
        ];
    }

    /**
     * @dataProvider roundingProvider
     * @param mixed $unroundedPrice
     * @param mixed $expectedAsPriceWithVat
     * @param mixed $expectedAsPriceWithoutVat
     * @param mixed $expectedAsVatAmount
     */
    public function testRounding(
        $unroundedPrice,
        $expectedAsPriceWithVat,
        $expectedAsPriceWithoutVat,
        $expectedAsVatAmount
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

        $rounding = new Rounding($pricingSettingMock);

        $this->assertThat($rounding->roundPriceWithVat($unroundedPrice), new IsMoneyEqual($expectedAsPriceWithVat));
        $this->assertThat($rounding->roundPriceWithoutVat($unroundedPrice), new IsMoneyEqual($expectedAsPriceWithoutVat));
        $this->assertThat($rounding->roundVatAmount($unroundedPrice), new IsMoneyEqual($expectedAsVatAmount));
    }

    public function roundingPriceWithVatProvider()
    {
        return [
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_INTEGER,
                'inputPrice' => Money::fromString('1.5'),
                'outputPrice' => Money::fromString('2'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_INTEGER,
                'inputPrice' => Money::fromString('1.49'),
                'outputPrice' => Money::fromString('1'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => Money::fromString('1.01'),
                'outputPrice' => Money::fromString('1.01'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => Money::fromString('1.009'),
                'outputPrice' => Money::fromString('1.01'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => Money::fromString('1.001'),
                'outputPrice' => Money::fromString('1'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => Money::fromString('1.24'),
                'outputPrice' => Money::fromString('1'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => Money::fromString('1.25'),
                'outputPrice' => Money::fromString('1.5'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => Money::fromString('1.74'),
                'outputPrice' => Money::fromString('1.5'),
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => Money::fromString('1.75'),
                'outputPrice' => Money::fromString('2'),
            ],
        ];
    }

    /**
     * @dataProvider roundingPriceWithVatProvider
     * @param mixed $roundingType
     * @param mixed $inputPrice
     * @param mixed $outputPrice
     */
    public function testRoundingPriceWithVat(
        $roundingType,
        $inputPrice,
        $outputPrice
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock->expects($this->any())->method('getRoundingType')->will($this->returnValue($roundingType));

        $rounding = new Rounding($pricingSettingMock);
        $roundedPrice = $rounding->roundPriceWithVat($inputPrice);

        $this->assertThat($roundedPrice, new IsMoneyEqual($outputPrice));
    }
}
