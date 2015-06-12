<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductEditFacadeTest extends DatabaseTestCase {

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

		$productEditData = new ProductEditData($productData);

		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$product = $productEditFacade->create($productEditData);

		$entityManager = $this->getContainer()->get(EntityManager::class);
		/* @var $entityManager \Doctrine\ORM\EntityManager */

		$entityManager->clear();

		$productFromDb = $productEditFacade->getById($product->getId());

		$this->assertSame($productFromDb->getCalculatedHidden(), $calculatedHidden);
		$this->assertSame($calculatedSellable, $productFromDb->getCalculatedSellable());
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function getTestHandleOutOfStockStateDataProvider() {
		return [
			[
				'hidden' => true,
				'sellable' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => true,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => false,
				'calculatedSellable' => true,
			],
			[
				'hidden' => true,
				'sellable' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => true,
				'calculatedSellable' => true,
			],
			[
				'hidden' => false,
				'sellable' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => false,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE,
				'calculatedHidden' => false,
				'calculatedSellable' => false,
			],
			[
				'hidden' => false,
				'sellable' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_HIDE,
				'calculatedHidden' => true,
				'calculatedSellable' => true,
			],
		];
	}
}
