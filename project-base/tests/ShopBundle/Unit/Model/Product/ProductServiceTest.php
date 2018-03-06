<?php

namespace Tests\ShopBundle\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\VatData;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductService;

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
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

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
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

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
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

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
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $productMock = $this->getMockBuilder(Product::class)
            ->setMethods(['markForVisibilityRecalculation', 'isMainVariant', 'isVariant'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $productMock->expects($this->any())->method('isMainVariant')->willReturn(false);
        $productMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(false);

        $productService->markProductForVisibilityRecalculation($productMock);
    }

    public function testMarkProductForVisibilityRecalculationMainVariant()
    {
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $variantMock = $this->getMockBuilder(Product::class)
            ->setMethods(['markForVisibilityRecalculation'])
            ->disableOriginalConstructor()
            ->getMock();
        $variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');

        $mainVariantMock = $this->getMockBuilder(Product::class)
            ->setMethods(['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getVariants'])
            ->disableOriginalConstructor()
            ->getMock();
        $mainVariantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $mainVariantMock->expects($this->atLeastOnce())->method('isMainVariant')->willReturn(true);
        $mainVariantMock->expects($this->any())->method('isVariant')->willReturn(false);
        $mainVariantMock->expects($this->atLeastOnce())->method('getVariants')->willReturn([$variantMock]);

        $productService->markProductForVisibilityRecalculation($mainVariantMock);
    }

    public function testMarkProductForVisibilityRecalculationVariant()
    {
        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $inputPriceCalculationMock = $this->getMockBuilder(InputPriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $basePriceCalculationMock = $this->getMockBuilder(BasePriceCalculation::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $pricingSettingMock = $this->getMockBuilder(PricingSetting::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $productService = new ProductService(
            $productPriceCalculationMock,
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            $productPriceRecalculationSchedulerMock
        );

        $mainVariantMock = $this->getMockBuilder(Product::class)
            ->setMethods(['markForVisibilityRecalculation'])
            ->disableOriginalConstructor()
            ->getMock();
        $mainVariantMock->expects($this->once())->method('markForVisibilityRecalculation');

        $variantMock = $this->getMockBuilder(Product::class)
            ->setMethods(['markForVisibilityRecalculation', 'isMainVariant', 'isVariant', 'getMainVariant'])
            ->disableOriginalConstructor()
            ->getMock();
        $variantMock->expects($this->atLeastOnce())->method('markForVisibilityRecalculation');
        $variantMock->expects($this->any())->method('isMainVariant')->willReturn(false);
        $variantMock->expects($this->atLeastOnce())->method('isVariant')->willReturn(true);
        $variantMock->expects($this->atLeastOnce())->method('getMainVariant')->willReturn($mainVariantMock);

        $productService->markProductForVisibilityRecalculation($variantMock);
    }

    public function testSortingProducts()
    {
        $productPriceCalculationMock = $this->createMock(ProductPriceCalculation::class);
        $inputPriceCalculationMock = $this->createMock(InputPriceCalculation::class);
        $basePriceCalculationMock = $this->createMock(BasePriceCalculation::class);
        $pricingSettingMock = $this->createMock(PricingSetting::class);
        $productPriceRecalculationSchedulerMock = $this->createMock(ProductPriceRecalculationScheduler::class);

        $productMock1 = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $productMock1->method('getId')->willReturn(1);

        $productMock2 = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
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
