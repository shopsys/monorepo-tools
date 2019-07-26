<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Functional\Model\Product\Availability;

use Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ProductAvailabilityRecalculatorTest extends TransactionFunctionalTestCase
{
    public function testRecalculateOnProductEditNotUsingStock()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator $productAvailabilityRecalculator */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);

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
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\AvailabilityFacade $availabilityFacade */
        $availabilityFacade = $this->getContainer()->get(AvailabilityFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator $productAvailabilityRecalculator */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);

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
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        /** @var \Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculator $productAvailabilityRecalculator */
        $productAvailabilityRecalculator = $this->getContainer()->get(ProductAvailabilityRecalculator::class);

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
