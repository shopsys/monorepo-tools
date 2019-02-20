<?php

namespace Tests\FrameworkBundle\Unit\Model\Payment;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Payment\PaymentData;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\FrameworkBundle\Model\Payment\PaymentPriceFactory;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Rounding;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Tests\FrameworkBundle\Test\IsMoneyEqual;

class PaymentPriceCalculationTest extends TestCase
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

    public function calculatePriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'priceWithoutVat' => Money::fromString('6998.78'),
                'priceWithVat' => Money::fromString('8469'),
                'productsPrice' => new Price(Money::fromInteger(100), Money::fromInteger(121)),
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'priceWithoutVat' => Money::fromString('5784.8'),
                'priceWithVat' => Money::fromString('7000'),
                'productsPrice' => new Price(Money::fromInteger(1000), Money::fromInteger(1210)),
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

        $paymentPriceCalculation = new PaymentPriceCalculation($basePriceCalculation, $pricingSettingMock);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $currency = new Currency(new CurrencyData());

        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $paymentData->vat = $vat;
        $payment = new Payment($paymentData);
        $payment->setPrice(new PaymentPriceFactory(new EntityNameResolver([])), $currency, Money::fromValue($inputPrice));

        $price = $paymentPriceCalculation->calculateIndependentPrice($payment, $currency);

        $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual($priceWithoutVat));
        $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual($priceWithVat));
    }

    /**
     * @dataProvider calculatePriceProvider
     * @param mixed $inputPriceType
     * @param mixed $inputPrice
     * @param mixed $vatPercent
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Component\Money\Money $priceWithVat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $productsPrice
     */
    public function testCalculatePrice(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        Money $priceWithoutVat,
        Money $priceWithVat,
        Price $productsPrice
    ) {
        $priceLimit = Money::fromInteger(1000);
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(['getInputPriceType', 'getRoundingType', 'getFreeTransportAndPaymentPriceLimit'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock
            ->expects($this->any())->method('getInputPriceType')
                ->will($this->returnValue($inputPriceType));
        $pricingSettingMock
            ->expects($this->any())->method('getRoundingType')
                ->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));
        $pricingSettingMock
            ->expects($this->any())->method('getFreeTransportAndPaymentPriceLimit')
                ->will($this->returnValue($priceLimit));

        $rounding = new Rounding($pricingSettingMock);

        $priceCalculation = new PriceCalculation($rounding);
        $basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

        $paymentPriceCalculation = new PaymentPriceCalculation($basePriceCalculation, $pricingSettingMock);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = $vatPercent;
        $vat = new Vat($vatData);
        $currency = new Currency(new CurrencyData());

        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $paymentData->vat = $vat;
        $payment = new Payment($paymentData);
        $payment->setPrice(new PaymentPriceFactory(new EntityNameResolver([])), $currency, Money::fromValue($inputPrice));

        $price = $paymentPriceCalculation->calculatePrice($payment, $currency, $productsPrice, 1);

        if ($productsPrice->getPriceWithVat()->isGreaterThan($priceLimit)) {
            $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual(Money::zero()));
            $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual(Money::zero()));
        } else {
            $this->assertThat($price->getPriceWithoutVat(), new IsMoneyEqual($priceWithoutVat));
            $this->assertThat($price->getPriceWithVat(), new IsMoneyEqual($priceWithVat));
        }
    }
}
