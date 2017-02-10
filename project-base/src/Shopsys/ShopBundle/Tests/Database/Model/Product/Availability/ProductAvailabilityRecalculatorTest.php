<?php

namespace Shopsys\ShopBundle\Tests\Database\Model\Product\Availability;

use Shopsys\ShopBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductEditDataFactory;
use Shopsys\ShopBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Tests\Test\DatabaseTestCase;

class ProductAvailabilityRecalculatorTest extends DatabaseTestCase
{

    public function testRecalculateOnProductEditNotUsingStock() {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = false;
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::ON_REQUEST), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockInStock() {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\ShopBundle\Model\Product\Availability\AvailabilityFacade */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 5;
        $productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($availabilityFacade->getDefaultInStockAvailability(), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockOutOfStock() {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\ShopBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getContainer()->get(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\ShopBundle\Model\Product\ProductEditDataFactory */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\ShopBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 0;
        $productEditData->productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY;
        $productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::OUT_OF_STOCK);
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::OUT_OF_STOCK), $productFromDb->getCalculatedAvailability());
    }

}
