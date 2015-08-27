<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductVariantService;

class ProductVariantServiceTest extends PHPUnit_Framework_TestCase {

	public function testCheckProductVariantTypeMainVariantException() {
		$productVariantService = new ProductVariantService();
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);

		$mainVariant->addVariant($variant);

		$this->setExpectedException(\SS6\ShopBundle\Model\Product\Exception\MainVariantCannotBeMainVariantException::class);
		$productVariantService->checkProductVariantType($mainVariant, []);
	}

	public function testCheckProductVariantMainVariantInVariantsException() {
		$productVariantService = new ProductVariantService();
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);
		$variant2 = new Product($productData);

		$mainVariant->addVariant($variant);
		$mainVariant->addVariant($variant2);

		$this->setExpectedException(\SS6\ShopBundle\Model\Product\Exception\MainVariantCannotBeVariantException::class);
		$productVariantService->checkProductVariantType($variant, [$variant, $variant2]);
	}

}
