<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product;

use Shopsys\ShopBundle\Component\Setting\Setting;
use Shopsys\ShopBundle\DataFixtures\Base\PricingGroupDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\ProductService;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductServiceTest extends DatabaseTestCase
{
    public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat() {
        $productService = $this->getContainer()->get(ProductService::class);
        /* @var $productService \Shopsys\ShopBundle\Model\Product\ProductService */
        $setting = $this->getContainer()->get(Setting::class);
        /* @var $setting \Shopsys\ShopBundle\Component\Setting\Setting */

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
        /* @var $productService \Shopsys\ShopBundle\Model\Product\ProductService */
        $setting = $this->getContainer()->get(Setting::class);
        /* @var $setting \Shopsys\ShopBundle\Component\Setting\Setting */

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
