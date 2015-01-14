<?php

namespace SS6\ShopBundle\Tests\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\BasePriceCalculation;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroup;
use SS6\ShopBundle\Model\Pricing\Group\PricingGroupData;
use SS6\ShopBundle\Model\Pricing\PriceCalculation;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Rounding;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductInputPriceRepository;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceProvider() {
		return array(
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT,
				'inputPrice' => '6999',
				'vatPercent' => '21',
				'pricingGroupCoefficient' => '1',
				'priceWithoutVat' => '6998.78',
				'priceWithVat' => '8469',
			),
			array(
				'inputPriceType' => PricingSetting::INPUT_PRICE_TYPE_WITH_VAT,
				'inputPrice' => '6999.99',
				'vatPercent' => '21',
				'pricingGroupCoefficient' => '2',
				'priceWithoutVat' => '11569.6',
				'priceWithVat' => '14000',
			),
		);
	}

	/**
	 * @dataProvider testCalculatePriceProvider
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
			->setMethods(array('getInputPriceType', 'getRoundingType'))
			->disableOriginalConstructor()
			->getMock();
		$pricingSettingMock
			->expects($this->any())->method('getInputPriceType')
				->will($this->returnValue($inputPriceType));
		$pricingSettingMock
			->expects($this->any())->method('getRoundingType')
				->will($this->returnValue(PricingSetting::ROUNDING_TYPE_INTEGER));

		$productInputPriceRepositoryMock = $this->getMockBuilder(ProductInputPriceRepository::class)
			->disableOriginalConstructor()
			->getMock();

		$rounding = new Rounding($pricingSettingMock);
		$priceCalculation = new PriceCalculation($rounding);
		$basePriceCalculation = new BasePriceCalculation($priceCalculation, $rounding);

		$productPriceCalculation = new ProductPriceCalculation(
			$basePriceCalculation,
			$pricingSettingMock,
			$productInputPriceRepositoryMock
		);

		$vat = new Vat(new VatData('vat', $vatPercent));
		$pricingGroup = new PricingGroup(new PricingGroupData('name', $pricingGroupCoefficient), 1);

		$product = new Product(new ProductData(['cs' => 'Product 1'], null, null, null, [], $inputPrice, $vat));

		$price = $productPriceCalculation->calculatePrice($product, $pricingGroup);

		$this->assertEquals(round($priceWithoutVat, 6), round($price->getPriceWithoutVat(), 6));
		$this->assertEquals(round($priceWithVat, 6), round($price->getPriceWithVat(), 6));
	}

}
