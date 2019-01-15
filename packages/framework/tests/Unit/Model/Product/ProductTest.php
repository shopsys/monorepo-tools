<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductTest extends TestCase
{
    public function testNoVariant()
    {
        $productData = new ProductData();
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $this->assertFalse($product->isVariant());
        $this->assertFalse($product->isMainVariant());
    }

    public function testIsVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->assertTrue($variant->isVariant());
        $this->assertFalse($variant->isMainVariant());
    }

    public function testIsMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->assertFalse($mainVariant->isVariant());
        $this->assertTrue($mainVariant->isMainVariant());
    }

    public function testGetMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->assertSame($mainVariant, $variant->getMainVariant());
    }

    public function testGetMainVariantException()
    {
        $productData = new ProductData();
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsNotVariantException::class);
        $product->getMainVariant();
    }

    public function testCreateVariantFromVariantException()
    {
        $productCategoryDomainFactory = new ProductCategoryDomainFactory();
        $productData = new ProductData();
        $variant = Product::create($productData, $productCategoryDomainFactory);
        $variant2 = Product::create($productData, $productCategoryDomainFactory);
        $mainVariant = Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant]);
        Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyVariantException::class);
        $mainVariant->addVariant($variant2, $productCategoryDomainFactory);
    }

    public function testCreateVariantFromMainVariantException()
    {
        $productCategoryDomainFactory = new ProductCategoryDomainFactory();
        $productData = new ProductData();
        $variant = Product::create($productData, $productCategoryDomainFactory);
        $variant2 = Product::create($productData, $productCategoryDomainFactory);
        $mainVariant = Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant]);
        $mainVariant2 = Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\MainVariantCannotBeVariantException::class);
        $mainVariant->addVariant($mainVariant2, $productCategoryDomainFactory);
    }

    public function testCreateMainVariantFromVariantException()
    {
        $productCategoryDomainFactory = new ProductCategoryDomainFactory();
        $productData = new ProductData();
        $variant = Product::create($productData, $productCategoryDomainFactory);
        $variant2 = Product::create($productData, $productCategoryDomainFactory);
        $variant3 = Product::create($productData, $productCategoryDomainFactory);
        Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant]);
        Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\VariantCanBeAddedOnlyToMainVariantException::class);
        $variant2->addVariant($variant3, $productCategoryDomainFactory);
    }

    public function testAddSelfAsVariantException()
    {
        $productCategoryDomainFactory = new ProductCategoryDomainFactory();
        $productData = new ProductData();
        $variant = Product::create($productData, $productCategoryDomainFactory);
        $mainVariant = Product::createMainVariant($productData, $productCategoryDomainFactory, [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\MainVariantCannotBeVariantException::class);
        $mainVariant->addVariant($mainVariant, $productCategoryDomainFactory);
    }

    public function testMarkForVisibilityRecalculation()
    {
        $productData = new ProductData();
        $product = Product::create($productData, new ProductCategoryDomainFactory());
        $product->markForVisibilityRecalculation();
        $this->assertTrue($product->isMarkedForVisibilityRecalculation());
    }

    public function testMarkForVisibilityRecalculationMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);
        $mainVariant->markForVisibilityRecalculation();
        $this->assertTrue($mainVariant->isMarkedForVisibilityRecalculation());
        $this->assertTrue($variant->isMarkedForVisibilityRecalculation());
    }

    public function testMarkForVisibilityRecalculationVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);
        $variant->markForVisibilityRecalculation();
        $this->assertTrue($variant->isMarkedForVisibilityRecalculation());
        $this->assertTrue($mainVariant->isMarkedForVisibilityRecalculation());
    }

    public function testDeleteResultNotVariant()
    {
        $productData = new ProductData();
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $this->assertEmpty($product->getProductDeleteResult()->getProductsForRecalculations());
    }

    public function testDeleteResultVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->assertSame([$mainVariant], $variant->getProductDeleteResult()->getProductsForRecalculations());
    }

    public function testDeleteResultMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->assertEmpty($mainVariant->getProductDeleteResult()->getProductsForRecalculations());
        $this->assertFalse($variant->isVariant());
    }

    public function testEditSchedulesPriceRecalculation()
    {
        $productPriceRecalculationSchedulerMock = $this->getMockBuilder(ProductPriceRecalculationScheduler::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceRecalculationSchedulerMock->expects($this->once())->method('scheduleProductForImmediateRecalculation');

        $productData = new ProductData();
        $product = Product::create($productData, new ProductCategoryDomainFactory());

        $product->edit(new ProductCategoryDomainFactory(), $productData, $productPriceRecalculationSchedulerMock);
    }

    public function testCheckIsNotMainVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException::class);
        $mainVariant->checkIsNotMainVariant();
    }

    public function testRefreshVariants()
    {
        $productData = new ProductData();
        $variant1 = Product::create($productData, new ProductCategoryDomainFactory());
        $variant2 = Product::create($productData, new ProductCategoryDomainFactory());
        $variant3 = Product::create($productData, new ProductCategoryDomainFactory());
        $mainVariant = Product::createMainVariant($productData, new ProductCategoryDomainFactory(), [$variant1, $variant2]);

        $currentVariants = [$variant2, $variant3];
        $mainVariant->refreshVariants($currentVariants, new ProductCategoryDomainFactory());

        $variantsArray = $mainVariant->getVariants();

        $this->assertNotContains($variant1, $variantsArray);
        $this->assertContains($variant2, $variantsArray);
        $this->assertContains($variant3, $variantsArray);
    }
}
