<?php

namespace Tests\ShopBundle\Functional\Model\Pricing;

use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\DataFixtures\Demo\PricingGroupDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ProductManualInputPriceTest extends TransactionFunctionalTestCase
{
    public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithoutVat()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting */
        $pricingSetting = $this->getContainer()->get(PricingSetting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation */
        $basePriceCalculation = $this->getContainer()->get(BasePriceCalculation::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation */
        $inputPriceCalculation = $this->getContainer()->get(InputPriceCalculation::class);

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
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);
        $inputPriceType = $pricingSetting->getInputPriceType();
        $productManualInputPrice->recalculateInputPriceForNewVatPercent($inputPriceType, 15, $basePriceCalculation, $inputPriceCalculation);

        $this->assertSame('1052.173913', (string)$productManualInputPrice->getInputPrice());
    }

    public function testRecalculateInputPriceForNewVatPercentWithInputPriceWithVat()
    {
        /** @var \Shopsys\FrameworkBundle\Component\Setting\Setting $setting */
        $setting = $this->getContainer()->get(Setting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting */
        $pricingSetting = $this->getContainer()->get(PricingSetting::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation */
        $basePriceCalculation = $this->getContainer()->get(BasePriceCalculation::class);
        /** @var \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation */
        $inputPriceCalculation = $this->getContainer()->get(InputPriceCalculation::class);

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
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $productManualInputPrice = new ProductManualInputPrice($product, $pricingGroup, 1000);

        $inputPriceType = $pricingSetting->getInputPriceType();
        $productManualInputPrice->recalculateInputPriceForNewVatPercent($inputPriceType, 15, $basePriceCalculation, $inputPriceCalculation);

        $this->assertSame('1000', (string)$productManualInputPrice->getInputPrice());
    }
}
