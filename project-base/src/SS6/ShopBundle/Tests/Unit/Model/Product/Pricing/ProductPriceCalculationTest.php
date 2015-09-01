<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Currency\Currency;
use SS6\ShopBundle\Model\Pricing\Currency\CurrencyFacade;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function calculatePriceProvider() {
		return [
			[
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
				'inputPrice' => '6999',
				'vatPercent' => '21',
				'pricingGroupCoefficient' => '1',
				'priceWithoutVat' => '6998.78',
				'priceWithVat' => '8469',
			],
			[
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
				'inputPrice' => '6999.99',
				'vatPercent' => '21',
				'pricingGroupCoefficient' => '2',
				'priceWithoutVat' => '11569.6',
				'priceWithVat' => '14000',
			],
		];
	}

	/**
	 * @dataProvider calculatePriceProvider
	 */
	public function testCalculatePrice(
		$inputPriceType,
		$inputPrice,
		$vatPercent,
		$pricingGroupCoefficient,
		$priceWithoutVat,
		$priceWithVat
	) {
		$pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
			->setMethods(['getInputPriceType', 'getRoundingType', 'getDomainDefaultCurrencyIdByDomainId'])
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getInputPriceType')
				->will($this->returnValue($inputPriceType));
		$pricingSettingMock
			->expects($this->any())->method('getRoundingType')
				->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));
		$pricingSettingMock
			->expects($this->any())->method('getDomainDefaultCurrencyIdByDomainId')
				->will($this->returnValue(1));

		$productManualInputPriceRepositoryMock = $this->getMockBuilder(ProductManualInputPriceRepository::class)
			->disableOriginalConstructor()
			->getMock();

		$currencyFacadeMock = $this->getMockBuilder(CurrencyFacade::class)
			->setMethods(['getById'])
			->disableOriginalConstructor()
			->getMock();

		$currencyMock = $this->getMockBuilder(Currency::class)
			->setMethods(['getReversedExchangeRate'])
			->disableOriginalConstructor()
			->getMock();

		$currencyMock
			->expects($this->any())->method('getReversedExchangeRate')
				->will($this->returnValue(1));

		$currencyFacadeMock
			->expects($this->any())->method('getById')
			->will($this->returnValue($currencyMock));

		$rounding = new Rounding($pricingSettingMock);
		$priceCalculation = new PriceCalculation($rounding);
		$basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

		$productPriceCalculation = new ProductPriceCalculation(
			$basePriceCalculation,
			$pricingSettingMock,

			$productManualInputPriceRepositoryMock,
			$currencyFacadeMock
		);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$pricingGroup = new PricingGroup(new PricingGroupData('name', $pricingGroupCoefficient), 1);

		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, $inputPrice, $vat));

		$price = $productPriceCalculation->calculatePrice($product, $pricingGroup->getDomainId(), $pricingGroup);

		$this->assertSame(round($priceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
		$this->assertSame(round($priceWithVat, 6), round($price->getPriceWithVat(), 6));
	}

}
