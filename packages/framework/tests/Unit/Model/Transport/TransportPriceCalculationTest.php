<?php

namespace Tests\FrameworkBundle\Unit\Model\Transport;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Transport\Transport;
use Shopsys\FrameworkBundle\Model\Transport\TransportData;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceCalculation;
use Shopsys\FrameworkBundle\Model\Transport\TransportPriceFactory;

class TransportPriceCalculationTest extends TestCase
{
    public function calculateIndependentPriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'priceWithoutVat' => Money::fromString('6998.78'),
                'priceWithVat' => Money::fromString('8469'),
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'priceWithoutVat' => Money::fromString('5784.8'),
                'priceWithVat' => Money::fromString('7000'),
            ],
        ];
    }

    /**
     * @dataProvider calculateIndependentPriceProvider
     * @param mixed $inputPriceType
     * @param mixed $inputPrice
     * @param mixed $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     */
    public function testCalculateIndependentPrice(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        Money $priceWithoutVat,
        Money $priceWithVat
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

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $currency = new Currency(new CurrencyData());

        $transportData = new TransportData();
        $transportData->name = ['cs' => 'transportName'];
        $transportData->vat = $vat;
        $transport = new Transport($transportData);
        $transport->setPrice(new TransportPriceFactory(new EntityNameResolver([])), $currency, Money::fromValue($inputPrice));

        $price = $transportPriceCalculation->calculateIndependentPrice($transport, $currency);

        $this->assertTrue($price->getPriceWithoutVat()->equals($priceWithoutVat));
        $this->assertTrue($price->getPriceWithVat()->equals($priceWithVat));
    }
}
