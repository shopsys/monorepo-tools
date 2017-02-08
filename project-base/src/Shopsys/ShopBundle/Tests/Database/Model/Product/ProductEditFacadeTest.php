<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Product;

use ReflectionClass;
use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\DataFixtures\Base\UnitDataFixture;
use SS6\ShopBundle\DataFixtures\Base\VatDataFixture;
use SS6\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Product\ProductEditData;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductEditFacadeTest extends DatabaseTestCase {

	/**
	 * @dataProvider getTestHandleOutOfStockStateDataProvider
	 */
	public function testHandleOutOfStockState(
		$hidden,
		$sellingDenied,
		$stockQuantity,
		$outOfStockAction,
		$calculatedHidden,
		$calculatedSellingDenied
	) {
		$productData = new ProductData();
		$productData->hidden = $hidden;
		$productData->sellingDenied = $sellingDenied;
		$productData->stockQuantity = $stockQuantity;
		$productData->outOfStockAction = $outOfStockAction;
		$productData->usingStock = true;
		$productData->availability = $this->getReference(AvailabilityDataFixture::IN_STOCK);
		$productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
		$productData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
		$productData->unit = $this->getReference(UnitDataFixture::PCS);

		$productEditData = new ProductEditData($productData);

		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */

		$product = $productEditFacade->create($productEditData);

		$entityManagerFacade = $this->getEntityManagerFacade();
		/* @var $entityManagerFacade \SS6\ShopBundle\Component\Doctrine\EntityManagerFacade */

		$entityManagerFacade->clear();

		$productFromDb = $productEditFacade->getById($product->getId());

		$this->assertSame($productFromDb->getCalculatedHidden(), $calculatedHidden);
		$this->assertSame($calculatedSellingDenied, $productFromDb->getCalculatedSellingDenied());
	}

	/**
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function getTestHandleOutOfStockStateDataProvider() {
		return [
			[
				'hidden' => true,
				'sellingDenied' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => true,
				'calculatedSellingDenied' => true,
			],
			[
				'hidden' => false,
				'sellingDenied' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => false,
				'calculatedSellingDenied' => false,
			],
			[
				'hidden' => true,
				'sellingDenied' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => true,
				'calculatedSellingDenied' => false,
			],
			[
				'hidden' => false,
				'sellingDenied' => true,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY,
				'calculatedHidden' => false,
				'calculatedSellingDenied' => true,
			],
			[
				'hidden' => false,
				'sellingDenied' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_EXCLUDE_FROM_SALE,
				'calculatedHidden' => false,
				'calculatedSellingDenied' => true,
			],
			[
				'hidden' => false,
				'sellingDenied' => false,
				'stockQuantity' => 0,
				'outOfStockAction' => Product::OUT_OF_STOCK_ACTION_HIDE,
				'calculatedHidden' => true,
				'calculatedSellingDenied' => false,
			],
		];
	}

	public function testEditMarkProductForVisibilityRecalculation() {
		$product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
		/* @var $product \SS6\ShopBundle\Model\Product\Product */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */

		$reflectionClass = new ReflectionClass(Product::class);
		$reflectionPropertyRecalculateVisibility = $reflectionClass->getProperty('recalculateVisibility');
		$reflectionPropertyRecalculateVisibility->setAccessible(true);
		$reflectionPropertyRecalculateVisibility->setValue($product, false);

		$productEditFacade->edit($product->getId(), $productEditDataFactory->createFromProduct($product));

		$this->assertSame(true, $reflectionPropertyRecalculateVisibility->getValue($product));
	}
}
