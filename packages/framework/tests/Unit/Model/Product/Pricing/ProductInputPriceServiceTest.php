<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductInputPriceService;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductManualInputPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductInputPriceServiceTest extends TestCase
{
    public function testGetManualInputPricesDataWithManualCalculationType()
    {
        $productData = new ProductData();
        $vatData = new VatData();
        $vatData->name = 'VatName';
        $vatData->percent = '10.0';
        $productData->vat = new Vat($vatData);
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock2 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceCalculationMock->expects($this->never())->method('calculatePrice');

        $inputPriceType = PricingSetting::INPUT_PRICE_TYPE_WITHOUT_VAT;

        $manualInputPrices = [
            new ProductManualInputPrice($product, $pricingGroups[0], '1000'),
            new ProductManualInputPrice($product, $pricingGroups[1], '2000'),
        ];

        $productInputPriceService = new ProductInputPriceService($inputPriceCalculation, $productPriceCalculationMock);
        $manualInputPricesData = $productInputPriceService->getManualInputPricesDataIndexedByPricingGroupId(
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
        $vatData = new VatData();
        $vatData->name = 'VatName';
        $vatData->percent = '10.0';
        $productData->vat = new Vat($vatData);
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId', 'getDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock1->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroupMock2 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId', 'getDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroupMock2->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
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
        $manualInputPricesData = $productInputPriceService->getManualInputPricesDataIndexedByPricingGroupId(
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
        $vatData = new VatData();
        $vatData->name = 'VatName';
        $vatData->percent = '10.0';
        $productData->vat = new Vat($vatData);
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_MANUAL;
        $product = Product::create($productData);

        $pricingGroupMock1 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId', 'getDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock1->expects($this->any())->method('getId')->willReturn(1);
        $pricingGroupMock1->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroupMock2 = $this->getMockBuilder(PricingGroup::class)
            ->setMethods(['getId', 'getDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupMock2->expects($this->any())->method('getId')->willReturn(2);
        $pricingGroupMock2->expects($this->any())->method('getDomainId')->willReturn(1);
        $pricingGroups = [$pricingGroupMock1, $pricingGroupMock2];

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
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
        $vatData = new VatData();
        $vatData->name = 'VatName';
        $vatData->percent = '10.0';
        $productData->vat = new Vat($vatData);
        $productData->priceCalculationType = Product::PRICE_CALCULATION_TYPE_AUTO;
        $productData->price = '1000';
        $product = Product::create($productData);

        $inputPriceCalculation = new InputPriceCalculation();
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
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
