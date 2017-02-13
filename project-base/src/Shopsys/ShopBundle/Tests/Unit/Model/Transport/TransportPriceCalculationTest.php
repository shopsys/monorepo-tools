<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Transport;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Transport\Transport;
use Shopsys\ShopBundle\Model\Transport\TransportData;
use Shopsys\ShopBundle\Model\Transport\TransportPriceCalculation;

class TransportPriceCalculationTest extends PHPUnit_Framework_TestCase
{
    public function calculateIndependentPriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'priceWithoutVat' => '6998.78',
                'priceWithVat' => '8469',
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'priceWithoutVat' => '5784.8',
                'priceWithVat' => '7000',
            ],
        ];
    }

    /**
     * @dataProvider calculateIndependentPriceProvider
     */
    public function testCalculateIndependentPrice(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        $priceWithoutVat,
        $priceWithVat
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getInputPriceType', 'getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getInputPriceType')
                ->will($this->returnValue($inputPriceType));
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

        $rounding = new Rounding($pricingSettingMock);
        $priceCalculation = new PriceCalculation($rounding);
        $basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

        $transportPriceCalculation = new TransportPriceCalculation($basePriceCalculation, $pricingSettingMock);

        $vat = new Vat(new VatData('vat', $vatPercent));
        $currency = new Currency(new CurrencyData());

        $transport = new Transport(new TransportData(['cs' => 'TransportName'], $vat));
        $transport->setPrice($currency, $inputPrice);

        $price = $transportPriceCalculation->calculateIndependentPrice($transport, $currency);

        $this->assertSame(round($priceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
        $this->assertSame(round($priceWithVat, 6), round($price->getPriceWithVat(), 6));
    }
}
