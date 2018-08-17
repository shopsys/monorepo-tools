<?php

namespace Tests\ShopBundle\Database\Model\Product\Availability;

use Shopsys\FrameworkBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\DatabaseTestCase;

class ProductAvailabilityRecalculatorTest extends DatabaseTestCase
{
    public function testRecalculateOnProductEditNotUsingStock()
    {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = false;
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockInStock()
    {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /* @var $availabilityFacade \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = true;
        $productData->stockQuantity = 5;
        $productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($availabilityFacade->getDefaultInStockAvailability(), $productFromDb->getCalculatedAvailability());
    }

    public function testRecalculateOnProductEditUsingStockOutOfStock()
    {
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /* @var $productFacade \Shopsys\FrameworkBundle\Model\Product\ProductFacade */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /* @var $productDataFactory \Shopsys\ShopBundle\Model\Product\ProductDataFactory */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);
        /* @var $productAvailabilityRecalculator \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator */

        $productId = 1;

        $product = $productFacade->getById($productId);

        $productData = $productDataFactory->createFromProduct($product);
        $productData->usingStock = true;
        $productData->stockQuantity = 0;
        $productData->outOfStockAction = Product::OUT_OF_STOCK_ACTION_SET_ALTERNATE_AVAILABILITY;
        $productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_ON_REQUEST);

        $productFacade->edit($productId, $productData);
        $productAvailabilityRecalculator->runAllScheduledRecalculations();
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $productFromDb = $productFacade->getById($productId);

        $this->assertSame($this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK), $productFromDb->getCalculatedAvailability());
    }
}
