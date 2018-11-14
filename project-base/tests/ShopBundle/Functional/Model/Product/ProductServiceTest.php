<?php

namespace Tests\ShopBundle\Functional\Model\Product;

use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductService;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ProductServiceTest extends TransactionFunctionalTestCase
{
    public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductService $productService */
        $productService = $this->getContainer()->get(ProductService::class);
        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        $producDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);

        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $productData = $producDataFactory->create();
        $productData->vat = $vat;
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);
        $product = Product::create($productData);

        $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);

        $productService->recalculateInputPriceForNewVatPercent($product, [$productManualInputPrice], 15);

        $this->assertSame('1052.173913', (string)$productManualInputPrice->getInputPrice());
    }

    public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithVat()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductService $productService */
        $productService = $this->getContainer()->get(ProductService::class);
        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $setting->set(PricingSetting::INPUT_PRICE_TYPE, PricingSetting::INPUT_PRICE_TYPE_WITH_VAT);

        $vatData = new VatData();
        $vatData->name = 'vat';
        $vatData->percent = 21;
        $vat = new Vat($vatData);

        $pricingGroup = $this->getReference(PricingGroupDataFixture::PRICING_GROUP_ORDINARY_DOMAIN_1);

        $productData = $productDataFactory->create();
        $productData->vat = $vat;
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);
        $product = Product::create($productData);

        $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);

        $productService->recalculateInputPriceForNewVatPercent($product, [$productManualInputPrice], 15);

        $this->assertSame('1000', (string)$productManualInputPrice->getInputPrice());
    }
}
