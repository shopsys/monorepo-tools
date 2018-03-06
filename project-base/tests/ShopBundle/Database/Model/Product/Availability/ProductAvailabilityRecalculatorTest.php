<?php

namespace Tests\ShopBundle\Database\Model\Product\Availability;

use Shopsys\FrameworkBundle\DataFixtures\Base\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductAvailabilityRecalculatorTest extends DatabaseTestCase
{
    public function testRecalculateOnProductEditNotUsingStock()
    {
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productAvailabilityRecalculator = $this->getServiceByType(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = false;
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockInStock()
    {
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $availabilityFacade = $this->getServiceByType(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade */
        $productAvailabilityRecalculator = $this->getServiceByType(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 5;
        $productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($availabilityFacade->getDefaultInStockAvailability(), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockOutOfStock()
    {
        $productFacade = $this->getServiceByType(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productEditDataFactory = $this->getServiceByType(ProductEditDataFactory::class);
        /* @var $productEditDataFactory \Shopsys\FrameworkBundle\Model\Product\ProductEditDataFactory */
        $productAvailabilityRecalculator = $this->getServiceByType(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productEditData = $productEditDataFactory->createFromProduct($product);
        $productEditData->productData->usingStock = true;
        $productEditData->productData->stockQuantity = 0;
        $productEditData->productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY;
        $productEditData->productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $productEditData->productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productEditData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManagerFacade()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK), $productFromDb->getCalculatedAvailability());
    }
}
