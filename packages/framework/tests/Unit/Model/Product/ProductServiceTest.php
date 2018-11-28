<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\PricingSetting;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductService;

class ProductServiceTest extends TestCase
{
    public function testEditSchedulesPriceRecalculation()
    {
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productData = new ProductData();
        $product = Product::create($productData);

        $product->edit(new ProductCategoryDomainFactory(), $productData, $productPriceRecalculationSchedulerMock);
    }

    public function testDeleteNotVariant()
    {
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

        $productService = new ProductService(
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            new ProductCategoryDomainFactory()
        );

        $productData = new ProductData();
        $product = Product::create($productData);

        $this->assertEmpty($productService->delete($product)->getProductsForRecalculations());
    }

    public function testDeleteVariant()
    {
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

        $productService = new ProductService(
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            new ProductCategoryDomainFactory()
        );

        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertSame([$mainVariant], $productService->delete($variant)->getProductsForRecalculations());
    }

    public function testDeleteMainVariant()
    {
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

        $productService = new ProductService(
            $inputPriceCalculationMock,
            $basePriceCalculationMock,
            $pricingSettingMock,
            new ProductCategoryDomainFactory()
        );

        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertEmpty($productService->delete($mainVariant)->getProductsForRecalculations());
        $this->assertFalse($variant->isVariant());
    }
}
