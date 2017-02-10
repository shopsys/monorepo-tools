<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Rounding;

class RoundingTest extends PHPUnit_Framework_TestCase
{
    public function roundingProvider() {
        return [
            [
                'unroundedPrice' => '0',
                'expectedAsPriceWithVat' => '0',
                'expectedAsPriceWithoutVat' => '0',
                'expectedAsVatAmount' => '0',
            ],
            [
                'unroundedPrice' => '1',
                'expectedAsPriceWithVat' => '1',
                'expectedAsPriceWithoutVat' => '1',
                'expectedAsVatAmount' => '1',
            ],
            [
                'unroundedPrice' => '0.999',
                'expectedAsPriceWithVat' => '1',
                'expectedAsPriceWithoutVat' => '1',
                'expectedAsVatAmount' => '1',
            ],
            [
                'unroundedPrice' => '0.99',
                'expectedAsPriceWithVat' => '1',
                'expectedAsPriceWithoutVat' => '0.99',
                'expectedAsVatAmount' => '0.99',
            ],
            [
                'unroundedPrice' => '0.5',
                'expectedAsPriceWithVat' => '1',
                'expectedAsPriceWithoutVat' => '0.50',
                'expectedAsVatAmount' => '0.50',
            ],
            [
                'unroundedPrice' => '0.49',
                'expectedAsPriceWithVat' => '0',
                'expectedAsPriceWithoutVat' => '0.49',
                'expectedAsVatAmount' => '0.49',
            ],
        ];
    }

    /**
     * @dataProvider roundingProvider
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

        $this->assertSame(round($expectedAsPriceWithVat, 6), round($rounding->roundPriceWithVat($unroundedPrice), 6));
        $this->assertSame(round($expectedAsPriceWithoutVat, 6), round($rounding->roundPriceWithoutVat($unroundedPrice), 6));
        $this->assertSame(round($expectedAsVatAmount, 6), round($rounding->roundVatAmount($unroundedPrice), 6));
    }

    public function roundingPriceWithVatProvider() {
        return [
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_INTEGER,
                'inputPrice' => 1.5,
                'outputPrice' => 2,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_INTEGER,
                'inputPrice' => 1.49,
                'outputPrice' => 1,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => 1.01,
                'outputPrice' => 1.01,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => 1.009,
                'outputPrice' => 1.01,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_HUNDREDTHS,
                'inputPrice' => 1.001,
                'outputPrice' => 1,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => 1.24,
                'outputPrice' => 1,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => 1.25,
                'outputPrice' => 1.5,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => 1.74,
                'outputPrice' => 1.5,
            ],
            [
                'roundingType' => PricingSetting::ROUNDING_TYPE_FIFTIES,
                'inputPrice' => 1.75,
                'outputPrice' => 2,
            ],
        ];
    }

    /**
     * @dataProvider roundingPriceWithVatProvider
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

        $this->assertSame(round($outputPrice, 6), round($roundedPrice, 6));
    }
}
