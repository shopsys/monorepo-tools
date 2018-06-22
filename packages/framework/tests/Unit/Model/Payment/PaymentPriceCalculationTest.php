<?php

namespace Tests\FrameworkBundle\Unit\Model\Payment;

use PHPUnit\Framework\TestCase;
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

class PaymentPriceCalculationTest extends TestCase
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

    public function calculatePriceProvider()
    {
        return [
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
                'inputPrice' => '6999',
                'vatPercent' => '21',
                'priceWithoutVat' => '6998.78',
                'priceWithVat' => '8469',
                'productsPrice' => new Price('100', '121'),
            ],
            [
                'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
                'inputPrice' => '6999.99',
                'vatPercent' => '21',
                'priceWithoutVat' => '5784.8',
                'priceWithVat' => '7000',
                'productsPrice' => new Price('1000', '1210'),
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

        $paymentPriceCalculation = new PaymentPriceCalculation($basePriceCalculation, $pricingSettingMock);

        $vat = new Vat(new VatData('vat', $vatPercent));
        $currency = new Currency(new CurrencyData());

        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $paymentData->vat = $vat;
        $payment = new Payment($paymentData);
        $payment->setPrice(new PaymentPriceFactory(), $currency, $inputPrice);

        $price = $paymentPriceCalculation->calculateIndependentPrice($payment, $currency);

        $this->assertSame(round($priceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
        $this->assertSame(round($priceWithVat, 6), round($price->getPriceWithVat(), 6));
    }

    /**
     * @dataProvider calculatePriceProvider
     */
    public function testCalculatePrice(
        $inputPriceType,
        $inputPrice,
        $vatPercent,
        $priceWithoutVat,
        $priceWithVat,
        $productsPrice
    ) {
        $priceLimit = 1000;
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

        $vat = new Vat(new VatData('vat', $vatPercent));
        $currency = new Currency(new CurrencyData());

        $paymentData = new PaymentData();
        $paymentData->name = ['cs' => 'paymentName'];
        $paymentData->vat = $vat;
        $payment = new Payment($paymentData);
        $payment->setPrice(new PaymentPriceFactory(), $currency, $inputPrice);

        $price = $paymentPriceCalculation->calculatePrice($payment, $currency, $productsPrice, 1);

        if ($productsPrice->getPriceWithVat() > $priceLimit) {
            $this->assertSame(round(0, 6), round($price->getPriceWithoutVat(), 6));
            $this->assertSame(round(0, 6), round($price->getPriceWithVat(), 6));
        } else {
            $this->assertSame(round($priceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
            $this->assertSame(round($priceWithVat, 6), round($price->getPriceWithVat(), 6));
        }
    }
}
