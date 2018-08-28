<?php

namespace Tests\FrameworkBundle\Unit\Model\Product;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductVariantService;

class ProductVariantServiceTest extends TestCase
{
    public function testCheckProductIsNotMainVariantException()
    {
        $productVariantService = new ProductVariantService(new ProductFactory(new EntityNameResolver([])));
        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException::class);
        $productVariantService->checkProductIsNotMainVariant($mainVariant);
    }

    public function testRefreshProductVariants()
    {
        $productVariantService = new ProductVariantService(new ProductFactory(new EntityNameResolver([])));
        $productData = new ProductData();
        $variant1 = Product::create($productData);
        $variant2 = Product::create($productData);
        $variant3 = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant1, $variant2]);

        $currentVariants = [$variant2, $variant3];
        $productVariantService->refreshProductVariants($mainVariant, $currentVariants);

        $variantsArray = $mainVariant->getVariants();

        $this->assertNotContains($variant1, $variantsArray);
        $this->assertContains($variant2, $variantsArray);
        $this->assertContains($variant3, $variantsArray);
    }

    public function testCreateVariant()
    {
        $mainVariantData = new ProductData();
        $mainProduct = Product::create(new ProductData());
        $variants = [];

        $productVariantService = new ProductVariantService(new ProductFactory(new EntityNameResolver([])));
        $mainVariant = $productVariantService->createMainVariant($mainVariantData, $mainProduct, $variants);

        $this->assertNotSame($mainProduct, $mainVariant);
        $this->assertTrue(in_array($mainProduct, $mainVariant->getVariants(), true));
    }
}
