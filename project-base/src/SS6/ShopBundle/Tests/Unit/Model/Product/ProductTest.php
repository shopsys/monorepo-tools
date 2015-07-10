<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductTest extends PHPUnit_Framework_TestCase {

	public function testNoVariant() {
		$productData = new ProductData();
		$product = new Product($productData);

		$this->assertFalse($product->isVariant());
		$this->assertFalse($product->isMainVariant());
	}

	public function testIsVariant() {
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);

		$mainVariant->addVariant($variant);

		$this->assertTrue($variant->isVariant());
		$this->assertFalse($variant->isMainVariant());
	}

	public function testIsMainVariant() {
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);

		$mainVariant->addVariant($variant);

		$this->assertFalse($mainVariant->isVariant());
		$this->assertTrue($mainVariant->isMainVariant());
	}

	public function testGetMainVariant() {
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);

		$mainVariant->addVariant($variant);

		$this->assertSame($mainVariant, $variant->getMainVariant());
	}

	public function testGetMainVariantException() {
		$productData = new ProductData();
		$product = new Product($productData);

		$this->setExpectedException(\SS6\ShopBundle\Model\Product\Exception\ProductIsNotVariantException::class);
		$product->getMainVariant();
	}

	public function testAddVariantToVariantException() {
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);
		$subVariant = new Product($productData);

		$mainVariant->addVariant($variant);
		$mainVariant->addVariant($subVariant);

		$this->setExpectedException(\SS6\ShopBundle\Model\Product\Exception\VariantCannotBeMainVariantException::class);
		$variant->addVariant($subVariant);
	}
}
