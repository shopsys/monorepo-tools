<?php

namespace Tests\ShopBundle\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class ProductTest extends PHPUnit_Framework_TestCase
{
    public function testNoVariant()
    {
        $productData = new ProductData();
        $product = Product::create($productData);

        $this->assertFalse($product->isVariant());
        $this->assertFalse($product->isMainVariant());
    }

    public function testIsVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        Product::createMainVariant($productData, [$variant]);

        $this->assertTrue($variant->isVariant());
        $this->assertFalse($variant->isMainVariant());
    }

    public function testIsMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertFalse($mainVariant->isVariant());
        $this->assertTrue($mainVariant->isMainVariant());
    }

    public function testGetMainVariant()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->assertSame($mainVariant, $variant->getMainVariant());
    }

    public function testGetMainVariantException()
    {
        $productData = new ProductData();
        $product = Product::create($productData);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsNotVariantException::class);
        $product->getMainVariant();
    }

    public function testCreateVariantFromVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $variant2 = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);
        Product::createMainVariant($productData, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\ProductIsAlreadyVariantException::class);
        $mainVariant->addVariant($variant2);
    }

    public function testCreateVariantFromMainVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $variant2 = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);
        $mainVariant2 = Product::createMainVariant($productData, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\MainVariantCannotBeVariantException::class);
        $mainVariant->addVariant($mainVariant2);
    }

    public function testCreateMainVariantFromVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $variant2 = Product::create($productData);
        $variant3 = Product::create($productData);
        Product::createMainVariant($productData, [$variant]);
        Product::createMainVariant($productData, [$variant2]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\VariantCanBeAddedOnlyToMainVariantException::class);
        $variant2->addVariant($variant3);
    }

    public function testAddSelfAsVariantException()
    {
        $productData = new ProductData();
        $variant = Product::create($productData);
        $mainVariant = Product::createMainVariant($productData, [$variant]);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Product\Exception\MainVariantCannotBeVariantException::class);
        $mainVariant->addVariant($mainVariant);
    }
}
