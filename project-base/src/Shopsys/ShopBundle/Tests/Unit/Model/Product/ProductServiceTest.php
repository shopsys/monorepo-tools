<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PricingSetting;
use Shopsys\ShopBundle\Model\Pricing\Vat\Vat;
use Shopsys\ShopBundle\Model\Pricing\Vat\VatData;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\ProductService;

class ProductServiceTest extends PHPUnit_Framework_TestCase
{
    public function testEditSchedulesPriceRecalculation()
    {
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $product = Product::create($productData);

        $productService->edit($product, $productData);
    }

    public function testSetInputPriceSchedulesPriceRecalculation()
    {
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $product = Product::create($productData);

        $productService->setInputPrice($product, 100);
    }

    public function testChangeVatSchedulesPriceRecalculation()
    {
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $product = Product::create($productData);

        $vatData = new VatData();
        $vat = new Vat($vatData);

        $productService->changeVat($product, $vat);
    }

    public function testDeleteNotVariant()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $product = Product::create($productData);

        $this->assertEmpty($productService->delete($product)->getProductsForRecalculations());
    }

    public function testDeleteVariant()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertSame([$mainVariant], $productService->delete($variant)->getProductsForRecalculations());
    }

    public function testDeleteMainVariant()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertEmpty($productService->delete($mainVariant)->getProductsForRecalculations());
        $this->assertFalse($variant->isVariant());
    }

    public function testMarkProductForVisibilityRecalculation()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productMock = $this->getMock(
            Product::class,
            ['markForVisibilityRecalculation', 'isMainVariant', 'isVariant'],
            [],
            '',
            false
        );
        $productMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $productMock->expects($this->any())->method('isMainVariant')->willReturn(false);
        $productMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(false);

        $productService->markProductForVisibilityRecalculation($productMock);
    }

    public function testMarkProductForVisibilityRecalculationMainVariant()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $variantMock = $this->getMock(Product::class, ['markForVisibilityRecalculation'], [], '', false);
        $variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');

        $mainVariantMock = $this->getMock(
            Product::class,
            ['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getVariants'],
            [],
            '',
            false
        );
        $mainVariantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $mainVariantMock->expects($this->atLeastOnce())->method('isMainVariant')->willReturn(true);
        $mainVariantMock->expects($this->any())->method('isVariant')->willReturn(false);
        $mainVariantMock->expects($this->atLeastOnce())->method('getVariants')->willReturn([$variantMock]);

        $productService->markProductForVisibilityRecalculation($mainVariantMock);
    }

    public function testMarkProductForVisibilityRecalculationVariant()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $mainVariantMock = $this->getMock(Product::class, ['markForVisibilityRecalculation'], [], '', false);
        $mainVariantMock->expects($this->once())->method('markForVisibilityRecalculation');

        $variantMock = $this->getMock(
            Product::class,
            ['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getMainVariant'],
            [],
            '',
            false
        );
        $variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $variantMock->expects($this->any())->method('isMainVariant')->willReturn(false);
        $variantMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(true);
        $variantMock->expects($this->atLeastOnce())->method('getMainVariant')->willReturn($mainVariantMock);

        $productService->markProductForVisibilityRecalculation($variantMock);
    }

    public function testSortingProducts()
    {
        $productPriceCalculationMock = $this->getMock(ProductPriceCalculation::class, null, [], '', false);
        $inputPriceCalculationMock = $this->getMock(InputPriceCalculation::class, null, [], '', false);
        $basePriceCalculationMock = $this->getMock(BasePriceCalculation::class, null, [], '', false);
        $pricingSettingMock = $this->getMock(PricingSetting::class, null, [], '', false);
        $productPriceRecalculationSchedulerMock = $this->getMock(ProductPriceRecalculationScheduler::class, null, [], '', false);

        $productMock1 = $this->getMock(Product::class, ['getId'], [], '', false);
        $productMock1->method('getId')->willReturn(1);

        $productMock2 = $this->getMock(Product::class, ['getId'], [], '', false);
        $productMock2->method('getId')->willReturn(2);

        $products = [$productMock1, $productMock2];

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $orderedProducts = $productService->sortProductsByProductIds(
            $products,
            [
                $productMock1->getId(),
                $productMock2->getId(),
            ]
        );
        $this->assertSame($productMock1, $orderedProducts[0]);
        $this->assertSame($productMock2, $orderedProducts[1]);

        $converselyOrderedProducts = $productService->sortProductsByProductIds(
            $products,
            [
                $productMock2->getId(),
                $productMock1->getId(),
            ]
        );
        $this->assertSame($productMock2, $converselyOrderedProducts[0]);
        $this->assertSame($productMock1, $converselyOrderedProducts[1]);
    }
}
