<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Product;

use ReflectionClass;
use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\UnitDataFixture;
use Shopsys\ShopBundle\DataFixtures\Base\VatDataFixture;
use Shopsys\ShopBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;
use Shopsys\ShopBundle\Model\Product\ProductEditData;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductEditFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

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
		/* @var $productEditFacade \Shopsys\ShopBundle\Model\Product\ProductEditFacade */

		$product = $productEditFacade->create($productEditData);

		$entityManagerFacade = $this->getEntityManagerFacade();
		/* @var $entityManagerFacade \Shopsys\ShopBundle\Component\Doctrine\EntityManagerFacade */

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
		/* @var $product \Shopsys\ShopBundle\Model\Product\Product */
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \Shopsys\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */

		$reflectionClass = new ReflectionClass(Product::class);
		$reflectionPropertyRecalculateVisibility = $reflectionClass->getProperty('recalculateVisibility');
		$reflectionPropertyRecalculateVisibility->setAccessible(true);
		$reflectionPropertyRecalculateVisibility->setValue($product, false);

		$productEditFacade->edit($product->getId(), $productEditDataFactory->createFromProduct($product));

		$this->assertSame(true, $reflectionPropertyRecalculateVisibility->getValue($product));
	}
}
