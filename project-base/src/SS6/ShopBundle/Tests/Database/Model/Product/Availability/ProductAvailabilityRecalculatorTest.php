<?php

namespace SS6\ShopBundle\Tests\Database\Model\Product\Availability;

use SS6\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductEditDataFactory;
use SS6\ShopBundle\Model\Product\ProductEditFacade;
use SS6\ShopBundle\Tests\Test\DatabaseTestCase;

/**
 * @UglyTest
 */
class ProductAvailabilityRecalculatorTest extends DatabaseTestCase {

	public function testRecalculateOnProductEditNotUsingStock() {
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

		$productId = 1;

		$product = $productEditFacade->getById($productId);

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = false;
		$productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

		$productEditFacade->edit($productId, $productEditData);
		$productAvailabilityRecalculator->runAllScheduledRecalculations();
		$this->getEntityManager()->flush();
		$this->getEntityManagerFacade()->clear();

		$productFromDb = $productEditFacade->getById($productId);

		$this->assertSame($this->getReference(AvailabilityDataFixture::ON_REQUEST), $productFromDb->getCalculatedAvailability());
	}

	public function testRecalculateOnProductEditUsingStockInStock() {
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
		/* @var $availabilityFacade \SS6\ShopBundle\Model\Product\Availability\AvailabilityFacade */
		$productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

		$productId = 1;

		$product = $productEditFacade->getById($productId);

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = true;
		$productEditData->productData->stockQuantity = 5;
		$productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
		$productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

		$productEditFacade->edit($productId, $productEditData);
		$productAvailabilityRecalculator->runAllScheduledRecalculations();
		$this->getEntityManager()->flush();
		$this->getEntityManagerFacade()->clear();

		$productFromDb = $productEditFacade->getById($productId);

		$this->assertSame($availabilityFacade->getDefaultInStockAvailability(), $productFromDb->getCalculatedAvailability());
	}

	public function testRecalculateOnProductEditUsingStockOutOfStock() {
		$productEditFacade = $this->getContainer()->get(ProductEditFacade::class);
		/* @var $productEditFacade \SS6\ShopBundle\Model\Product\ProductEditFacade */
		$productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
		/* @var $productEditDataFactory \SS6\ShopBundle\Model\Product\ProductEditDataFactory */
		$productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
		/* @var $productAvailabilityRecalculator \SS6\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

		$productId = 1;

		$product = $productEditFacade->getById($productId);

		$productEditData = $productEditDataFactory->createFromProduct($product);
		$productEditData->productData->usingStock = true;
		$productEditData->productData->stockQuantity = 0;
		$productEditData->productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY;
		$productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
		$productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

		$productEditFacade->edit($productId, $productEditData);
		$productAvailabilityRecalculator->runAllScheduledRecalculations();
		$this->getEntityManager()->flush();
		$this->getEntityManagerFacade()->clear();

		$productFromDb = $productEditFacade->getById($productId);

		$this->assertSame($this->getReference(AvailabilityDataFixture::OUT_OF_STOCK), $productFromDb->getCalculatedAvailability());
	}

}
