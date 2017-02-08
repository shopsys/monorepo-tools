<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Payment;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Payment\Payment;
use Shopsys\ShopBundle\Model\Payment\PaymentData;
use Shopsys\ShopBundle\Model\Payment\PaymentPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Currency\Currency;
use Shopsys\ShopBundle\Model\Pricing\Currency\CurrencyData;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Rounding;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;

class PaymentPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function calculateIndependentPriceProvider() {
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

	public function calculatePriceProvider() {
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

		$payment = new Payment(new PaymentData(['cs' => 'paymentName'], $vat));
		$payment->setPrice($currency, $inputPrice);

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

		$payment = new Payment(new PaymentData(['cs' => 'paymentName'], $vat));
		$payment->setPrice($currency, $inputPrice);

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
