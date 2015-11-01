<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product\BestsellingProduct;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\BestsellingProduct\BestsellingProductService;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class BestsellingProductServiceTest extends PHPUnit_Framework_TestCase {

	public function testCombineManualAndAutomaticBestsellingProducts() {
		$bestsellingProductService = new BestsellingProductService();

		$maxResults = 4;

		$price = 100;
		$vat = new Vat(new VatData('vat', 21));
		$productData = new ProductData();
		$productData->name = ['cs' => 'Product 1'];
		$productData->price = $price;
		$productData->vat = $vat;
		$product1 = new Product($productData);
		$product2 = new Product($productData);
		$product3 = new Product($productData);
		$product4 = new Product($productData);
		$product5 = new Product($productData);

		$manualBestsellingProductsIndexedByPosition = [
			0 => $product1,
			2 => $product2,
		];

		$automaticBestsellingProducts = [
			$product1,
			$product3,
			$product4,
			$product5,
		];

		$combinedArray = $bestsellingProductService->combineManualAndAutomaticBestsellingProducts(
			$manualBestsellingProductsIndexedByPosition,
			$automaticBestsellingProducts,
			$maxResults
		);

		$combinedArrayExpected = [
			0 => $product1,
			1 => $product3,
			2 => $product2,
			3 => $product4,
		];

		$this->assertEquals($combinedArray, $combinedArrayExpected);
	}

}
