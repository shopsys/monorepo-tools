<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product;

use SS6\ShopBundle\Component\Setting\Setting;
use SS6\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture;
use SS6\ShopBundle\Model\Pricing\PricingSetting;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Pricing\ProductManualInputPrice;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductService;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class ProductServiceTest extends DatabaseTestCase {

	public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat() {
		$productService = $this->getContainer()->get(ProductService::class);
		/* @var $productService \SS6\ShopBundle\Model\Product\ProductService */
		$setting = $this->getContainer()->get(Setting::class);
		/* @var $setting \SS6\ShopBundle\Component\Setting\Setting */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);

		$vatData = new VatData('vat', 21);
		$vat = new Vat($vatData);

		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);

		$productData = new ProductData();
		$productData->price = 1000;
		$productData->vat = $vat;
		$productData->unit = $this->getReference(UnitDataFixture::PCS);
		$product = Product::create($productData);

		$productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);

		$productService->recalculateInputPriceForNewVatPercent($product, [$productManualInputPrice], 15);

		$this->assertSame('1052.173913', (string)$product->getPrice());
		$this->assertSame('1052.173913', (string)$productManualInputPrice->getInputPrice());
	}

	public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithVat() {
		$productService = $this->getContainer()->get(ProductService::class);
		/* @var $productService \SS6\ShopBundle\Model\Product\ProductService */
		$setting = $this->getContainer()->get(Setting::class);
		/* @var $setting \SS6\ShopBundle\Component\Setting\Setting */

		$setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);

		$vatData = new VatData('vat', 21);
		$vat = new Vat($vatData);

		$pricingGroup = $this->getReference(PricingGroupDataFixture::ORDINARY_DOMAIN_1);

		$productData = new ProductData();
		$productData->price = 1000;
		$productData->vat = $vat;
		$productData->unit = $this->getReference(UnitDataFixture::PCS);
		$product = Product::create($productData);

		$productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);

		$productService->recalculateInputPriceForNewVatPercent($product, [$productManualInputPrice], 15);

		$this->assertSame('1000', (string)$product->getPrice());
		$this->assertSame('1000', (string)$productManualInputPrice->getInputPrice());
	}

}
