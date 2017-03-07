<?php

namespace Tests\ShopBundle\Unit\Model\Product\Pricing;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductInputPriceService;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;

class ProductInputPriceServiceTest extends PHPUnit_Framework_TestCase
{
    public function testGetManualInputPricesDataWithManualCalculationType()
    {
        $productData = new ProductData();
        $productData->vat = new Vat(new VatData('VatName', '10.0'));
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMock(PricingGroup::class, ['getId'], [], '', false);
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock2 = $this->getMock(PricingGroup::class, ['getId'], [], '', false);
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock->expects($this->never())->method('calculatePrice');

        $inputPriceType = PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT;

        $manualInputPrices = [
            new ProductManualInputPrice($product, $pricingGroups[0], '1000'),
            new ProductManualInputPrice($product, $pricingGroups[1], '2000'),
        ];

        $productInputPriceService = new ProductInputPriceService($inputPriceCalculation, $productPriceCalculationMock);
        $manualInputPricesData = $productInputPriceService->getManualInputPricesData(
            $product,
            $inputPriceType,
            $pricingGroups,
            $manualInputPrices
        );

        $this->assertCount(2, $manualInputPricesData);
        $this->assertSame('1000', $manualInputPricesData[$pricingGroups[0]->getId()]);
        $this->assertSame('2000', $manualInputPricesData[$pricingGroups[1]->getId()]);
    }

    public function testGetManualInputPricesDataWithAutoCalculationType()
    {
        $productData = new ProductData();
        $productData->vat = new Vat(new VatData('VatName', '10.0'));
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMock(PricingGroup::class, ['getId', 'getDomainId'], [], '', false);
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock1->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroupMock2 = $this->getMock(PricingGroup::class, ['getId', 'getDomainId'], [], '', false);
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroupMock2->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock
            ->expects($this->any())
            ->method('calculatePrice')
            ->willReturnMap([
                [$product, 1, $pricingGroups[0], new ProductPrice(new Price('1000', '1100'), false)],
                [$product, 1, $pricingGroups[1], new ProductPrice(new Price('2000', '2200'), false)],
            ]);

        $inputPriceType = PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT;

        $manualInputPrices = [
            new ProductManualInputPrice($product, $pricingGroups[0], '1000'),
            new ProductManualInputPrice($product, $pricingGroups[1], '2000'),
        ];

        $productInputPriceService = new ProductInputPriceService($inputPriceCalculation, $productPriceCalculationMock);
        $manualInputPricesData = $productInputPriceService->getManualInputPricesData(
            $product,
            $inputPriceType,
            $pricingGroups,
            $manualInputPrices
        );

        $this->assertCount(2, $manualInputPricesData);
        $this->assertSame('1000', (string)$manualInputPricesData[$pricingGroups[0]->getId()]);
        $this->assertSame('2000', (string)$manualInputPricesData[$pricingGroups[1]->getId()]);
    }

    public function testGetInputPriceWithManualCalculationType()
    {
        $productData = new ProductData();
        $productData->vat = new Vat(new VatData('VatName', '10.0'));
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMock(PricingGroup::class, ['getId', 'getDomainId'], [], '', false);
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock1->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroupMock2 = $this->getMock(PricingGroup::class, ['getId', 'getDomainId'], [], '', false);
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroupMock2->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock
            ->expects($this->any())
            ->method('calculatePrice')
            ->willReturnMap([
                [$product, 1, $pricingGroups[0], new ProductPrice(new Price('1000', '1100'), false)],
                [$product, 1, $pricingGroups[1], new ProductPrice(new Price('2000', '2200'), false)],
            ]);

        $inputPriceType = PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT;

        $manualInputPrices = [
            new ProductManualInputPrice($product, $pricingGroups[0], '1000'),
            new ProductManualInputPrice($product, $pricingGroups[1], '2000'),
        ];

        $productInputPriceService = new ProductInputPriceService($inputPriceCalculation, $productPriceCalculationMock);
        $inputPrice = $productInputPriceService->getInputPrice(
            $product,
            $inputPriceType,
            $manualInputPrices
        );

        $this->assertSame('2000', (string)$inputPrice);
    }

    public function testGetInputPriceWithAutoCalculationType()
    {
        $productData = new ProductData();
        $productData->vat = new Vat(new VatData('VatName', '10.0'));
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $productData->price = '1000';
        $product = Product::create($productData);

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, ['calculatePrice'], [], '', false);
        $productPriceCalculationMock->expects($this->never())->method('calculatePrice');

        $inputPriceType = PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT;

        $manualInputPrices = [];

        $productInputPriceService = new ProductInputPriceService($inputPriceCalculation, $productPriceCalculationMock);
        $inputPrice = $productInputPriceService->getInputPrice(
            $product,
            $inputPriceType,
            $manualInputPrices
        );

        $this->assertSame('1000', (string)$inputPrice);
    }
}
