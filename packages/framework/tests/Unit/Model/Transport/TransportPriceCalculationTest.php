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
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class TransportPriceCalculationTest extends TestCase
{
    public function calculateIndependentPriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => Money::create(6999),
                'vatPercent' => '21',
                'priceWithoutVat' => Money::create('6999.17'),
                'priceWithVat' => Money::create(8469),
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => Money::create('6999.99'),
                'vatPercent' => '21',
                'priceWithoutVat' => Money::create('5785.12'),
                'priceWithVat' => Money::create(7000),
            ],
        ];
    }

    /**
     * @dataProvider calculateIndependentPriceProvider
     * @param int $inputPriceType
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $inputPrice
     * @param string $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     */
    public function testCalculateIndependentPrice(
        int $inputPriceType,
        Money $inputPrice,
        string $vatPercent,
        Money $priceWithoutVat,
        Money $priceWithVat
    ) {
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getInputPriceType', 'getRoundingType'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getInputPriceType')
                ->willReturn($inputPriceType);
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->willReturn(PricingSetting::ROUNDING_TYPE_INTEGER);

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
        $transport->setPrice(new TransportPriceFactory(new EntityNameResolver([])), $currency, $inputPrice);

        $price = $transportPriceCalculation->calculateIndependentPrice($transport, $currency);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual($priceWithoutVat));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual($priceWithVat));
    }
}
