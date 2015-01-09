<?php

namespace SS6\ShopBundle\TestsDb\Model\Product;

use SS6\ShopBundle\Component\Test\DatabaseTestCase;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Setting\SettingValue;

class ProductServiceTest extends DatabaseTestCase {

	public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat() {
		$productService = $this->getContainer()->get('ss6.shop.product.product_service');
		/* @var $productService \SS6\ShopBundle\Model\Product\ProductService */
		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT, SettingValue::DOMAIN_ID_COMMON);

		$vatData = new VatData('vat', 21);
		$vat = new Vat($vatData);

		$productData = new ProductData();
		$productData->price = 1000;
		$productData->vat = $vat;
		$product = new Product($productData);

		$productService->recalculateInputPriceForNewVatPercent($product, 15);

		$this->assertEquals('1052.173913', round($product->getPrice(), 6));
	}

	public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithVat() {
		$productService = $this->getContainer()->get('ss6.shop.product.product_service');
		/* @var $productService \SS6\ShopBundle\Model\Product\ProductService */
		$setting = $this->getContainer()->get('ss6.shop.setting');
		/* @var $setting \SS6\ShopBundle\Model\Setting\Setting */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT, SettingValue::DOMAIN_ID_COMMON);

		$vatData = new VatData('vat', 21);
		$vat = new Vat($vatData);

		$productData = new ProductData();
		$productData->price = 1000;
		$productData->vat = $vat;
		$product = new Product($productData);

		$productService->recalculateInputPriceForNewVatPercent($product, 15);

		$this->assertEquals('1000', round($product->getPrice(), 6));
	}

}
