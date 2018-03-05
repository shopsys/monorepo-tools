<?php

namespace Tests\ShopBundle\Unit\Component\Transformers;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Transformers\ProductIdToProductTransformer;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductRepository;

class ProductIdToProductTransformerTest extends TestCase
{
    public function testTransform()
    {
        $productId = 1;
        $product = $this->getMockBuilder(Product::class)
            ->setMethods(['getId'])
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->atLeastOnce())->method('getId')->willReturn($productId);

        $productRepository = $this->createMock(ProductRepository::class);
        $productIdToProductTransformer = new ProductIdToProductTransformer($productRepository);

        $this->assertSame($productId, $productIdToProductTransformer->transform($product));
        $this->assertNull($productIdToProductTransformer->transform(null));
    }

    public function testReverseTransform()
    {
        $productId = 1;
        $product = $this->getMockBuilder(Product::class);

        $productsRepositoryGetByIdValues = [
            [$productId, $product],
        ];

        $productRepository = $this->getMockBuilder(ProductRepository::class)
            ->setMethods(['getById'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepository->expects($this->atLeastOnce())->method('getById')->willReturnMap($productsRepositoryGetByIdValues);

        $productIdToProductTransformer = new ProductIdToProductTransformer($productRepository);

        $this->assertSame($product, $productIdToProductTransformer->reverseTransform($productId));
        $this->assertNull($productIdToProductTransformer->reverseTransform(null));
    }
}
