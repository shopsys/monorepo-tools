<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class ProductTest extends PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getTestHandleOutOfStockStateDataProvider
	 */
	public function testHandleOutOfStockState(
		$hidden,
		$sellable,
		$stockQuantity,
		$outOfStockAction,
		$calculatedHidden,
		$calculatedSellable
	) {
		$productData = new ProductData();
		$productData->hidden = $hidden;
		$productData->sellable = $sellable;
		$productData->stockQuantity = $stockQuantity;
		$productData->outOfStockAction = $outOfStockAction;
		$productData->usingStock = true;

		$product = new Product($productData);

		$this->assertSame($product->getCalculatedHidden(), $calculatedHidden);
		$this->assertSame($product->getCalculatedSellable(), $calculatedSellable);
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function getTestHandleOutOfStockStateDataProvider() {
		return [
			[
				'hidden' => true,
				'sellable' => false,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE,
				'calculatedHidden' => true,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE,
				'calculatedHidden' => false,
				'calculatedSellable' => true,
			],
			[
				'hidden' => true,
				'sellable' => true,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE,
				'calculatedHidden' => true,
				'calculatedSellable' => true,
			],
			[
				'hidden' => false,
				'sellable' => false,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE,
				'calculatedHidden' => false,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE,
				'calculatedHidden' => false,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => null,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_HIDE,
				'calculatedHidden' => true,
				'calculatedSellable' => true,
			],
		];
	}
}