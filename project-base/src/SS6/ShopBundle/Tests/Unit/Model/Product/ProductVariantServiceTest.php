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

}
