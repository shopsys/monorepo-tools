<?php

namespace Tests\ShopBundle\Functional\Model\Product;

use ReflectionClass;
use Shopsys\FrameworkBundle\DataFixtures\Demo\AvailabilityDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\ProductDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\UnitDataFixture;
use Shopsys\FrameworkBundle\DataFixtures\Demo\VatDataFixture;
use Shopsys\FrameworkBundle\Model\Product\ProductDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Product\ProductFacade;
use Shopsys\ShopBundle\Model\Product\Product;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class ProductFacadeTest extends TransactionFunctionalTestCase
{
    /**
     * @dataProvider getTestHandleOutOfStockStateDataProvider
     * @param mixed $hidden
     * @param mixed $sellingDenied
     * @param mixed $stockQuantity
     * @param mixed $outOfStockAction
     * @param mixed $calculatedHidden
     * @param mixed $calculatedSellingDenied
     */
    public function testHandleOutOfStockState(
        $hidden,
        $sellingDenied,
        $stockQuantity,
        $outOfStockAction,
        $calculatedHidden,
        $calculatedSellingDenied
    ) {
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);
        $productData = $productDataFactory->create();
        $productData->hidden = $hidden;
        $productData->sellingDenied = $sellingDenied;
        $productData->stockQuantity = $stockQuantity;
        $productData->outOfStockAction = $outOfStockAction;
        $productData->usingStock = true;
        $productData->availability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_IN_STOCK);
        $productData->outOfStockAvailability = $this->getReference(AvailabilityDataFixture::AVAILABILITY_OUT_OF_STOCK);
        $productData->vat = $this->getReference(VatDataFixture::VAT_HIGH);
        $productData->unit = $this->getReference(UnitDataFixture::UNIT_PIECES);

        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);

        $product = $productFacade->create($productData);

        $this->getEntityManager()->clear();

        $productFromDb = $productFacade->getById($product->getId());

        $this->assertSame($productFromDb->getCalculatedHidden(), $calculatedHidden);
        $this->assertSame($calculatedSellingDenied, $productFromDb->getCalculatedSellingDenied());
    }

    public function getTestHandleOutOfStockStateDataProvider()
    {
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

    public function testEditMarkProductForVisibilityRecalculation()
    {
        /** @var \Shopsys\ShopBundle\Model\Product\Product $product */
        $product = $this->getReference(ProductDataFixture::PRODUCT_PREFIX . '1');
        /** @var \Shopsys\FrameworkBundle\Model\Product\ProductFacade $productFacade */
        $productFacade = $this->getContainer()->get(ProductFacade::class);
        /** @var \Shopsys\ShopBundle\Model\Product\ProductDataFactory $productDataFactory */
        $productDataFactory = $this->getContainer()->get(ProductDataFactoryInterface::class);

        $reflectionClass = new ReflectionClass(Product::class);
        $reflectionPropertyRecalculateVisibility = $reflectionClass->getProperty('recalculateVisibility');
        $reflectionPropertyRecalculateVisibility->setAccessible(true);
        $reflectionPropertyRecalculateVisibility->setValue($product, false);

        $productFacade->edit($product->getId(), $productDataFactory->createFromProduct($product));

        $this->assertSame(true, $reflectionPropertyRecalculateVisibility->getValue($product));
    }
}
