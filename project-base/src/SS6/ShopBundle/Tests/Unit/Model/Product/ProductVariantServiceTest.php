<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductVariantService;

class ProductVariantServiceTest extends PHPUnit_Framework_TestCase {

	public function testCheckProductIsNotMainVariantException() {
		$productVariantService = new ProductVariantService();
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant = new Product($productData);

		$mainVariant->addVariant($variant);

		$this->setExpectedException(\SS6\ShopBundle\Model\Product\Exception\ProductIsAlreadyMainVariantException::class);
		$productVariantService->checkProductIsNotMainVariant($mainVariant);
	}

	public function testRefreshProductVariants() {
		$productVariantService = new ProductVariantService();
		$productData = new ProductData();
		$mainVariant = new Product($productData);
		$variant1 = new Product($productData);
		$variant2 = new Product($productData);
		$variant3 = new Product($productData);

		$mainVariant->addVariant($variant1);
		$mainVariant->addVariant($variant2);

		$currentVariants = [$variant2, $variant3];
		$productVariantService->refreshProductVariants($mainVariant, $currentVariants);

		$variantsArray = $mainVariant->getVariants()->toArray();

		$this->assertNotContains($variant1, $variantsArray);
		$this->assertContains($variant2, $variantsArray);
		$this->assertContains($variant3, $variantsArray);
	}

}
