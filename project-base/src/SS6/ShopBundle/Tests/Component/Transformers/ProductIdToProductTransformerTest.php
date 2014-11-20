<?php

namespace SS6\ShopBundle\Tests\Component\Transformers;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\ProductRepository;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Component\Transformers\ProductIdToProductTransformer;

class ProductIdToProductTransformerTest extends PHPUnit_Framework_TestCase {

	public function testProductIdToProductTransformer() {

		$product = $this->getMockBuilder(Product::class)
			->setMethods(['getId'])
			->disableOriginalConstructor()
			->getMock();
		$product->expects($this->atLeastOnce())->method('getId')->willReturn(1);

		$productsRepositoryGetByIdValues = [
			[1, $product],
			[99999, null]
		];

		$productRepository = $this->getMockBuilder(ProductRepository::class)
			->setMethods(['getById'])
			->disableOriginalConstructor()
			->getMock();
		$productRepository->expects($this->atLeastOnce())->method('getById')->willReturnMap($productsRepositoryGetByIdValues);

		$productIdToProductTransformer = new ProductIdToProductTransformer($productRepository);

		$this->assertEquals(1, $productIdToProductTransformer->transform($product));
		$this->assertEquals(null, $productIdToProductTransformer->transform(null));
		$this->assertEquals($product, $productIdToProductTransformer->reverseTransform(1));
		$this->assertEquals(null, $productIdToProductTransformer->reverseTransform(null));
	}
}