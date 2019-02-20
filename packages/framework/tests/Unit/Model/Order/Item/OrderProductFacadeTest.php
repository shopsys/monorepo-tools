<?php

namespace Tests\FrameworkBundle\Unit\Model\Order\Item;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Module\ModuleFacade;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItem;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Availability\ProductAvailabilityRecalculationScheduler;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomainFactory;
use Shopsys\FrameworkBundle\Model\Product\ProductData;
use Shopsys\FrameworkBundle\Model\Product\ProductHiddenRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductSellingDeniedRecalculator;
use Shopsys\FrameworkBundle\Model\Product\ProductVisibilityFacade;

class OrderProductFacadeTest extends TestCase
{
    public function testSubtractOrderProductsFromStockUsingStock()
    {
        $productStockQuantity = 15;
        $orderProductQuantity = 10;

        $orderMock = $this->createMock(Order::class);

        $productData = new ProductData();
        $productData->usingStock = true;
        $productData->stockQuantity = $productStockQuantity;
        $product = Product::create($productData, new ProductCategoryDomainFactory());
        $productPrice = new Price(Money::zero(), Money::zero());

        $orderProduct = new OrderItem($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, OrderItem::TYPE_PRODUCT, null, null);
        $orderProduct->setProduct($product);

        $orderProductFacade = $this->createOrderProductFacade();
        $orderProductFacade->subtractOrderProductsFromStock([$orderProduct]);

        $this->assertSame($productStockQuantity - $orderProductQuantity, $product->getStockQuantity());
    }

    public function testSubtractOrderProductsFromStockNotUsingStock()
    {
        $productStockQuantity = 15;
        $orderProductQuantity = 10;

        $orderMock = $this->createMock(Order::class);

        $productData = new ProductData();
        $productData->usingStock = false;
        $productData->stockQuantity = $productStockQuantity;
        $product = Product::create($productData, new ProductCategoryDomainFactory());
        $productPrice = new Price(Money::zero(), Money::zero());

        $orderProduct = new OrderItem($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, OrderItem::TYPE_PRODUCT, null, null);
        $orderProduct->setProduct($product);

        $orderProductFacade = $this->createOrderProductFacade();
        $orderProductFacade->subtractOrderProductsFromStock([$orderProduct]);

        $this->assertSame($productStockQuantity, $product->getStockQuantity());
    }

    public function testAddOrderProductsToStockUsingStock()
    {
        $productStockQuantity = 15;
        $orderProductQuantity = 10;

        $orderMock = $this->createMock(Order::class);

        $productData = new ProductData();
        $productData->usingStock = true;
        $productData->stockQuantity = $productStockQuantity;
        $product = Product::create($productData, new ProductCategoryDomainFactory());
        $productPrice = new Price(Money::zero(), Money::zero());

        $orderProduct = new OrderItem($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, OrderItem::TYPE_PRODUCT, null, null);
        $orderProduct->setProduct($product);

        $orderProductFacade = $this->createOrderProductFacade();
        $orderProductFacade->addOrderProductsToStock([$orderProduct]);

        $this->assertSame($productStockQuantity + $orderProductQuantity, $product->getStockQuantity());
    }

    public function testAddOrderProductsToStockNotUsingStock()
    {
        $productStockQuantity = 15;
        $orderProductQuantity = 10;

        $orderMock = $this->createMock(Order::class);

        $productData = new ProductData();
        $productData->usingStock = false;
        $productData->stockQuantity = $productStockQuantity;
        $product = Product::create($productData, new ProductCategoryDomainFactory());
        $productPrice = new Price(Money::zero(), Money::zero());

        $orderProduct = new OrderItem($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, OrderItem::TYPE_PRODUCT, null, null);
        $orderProduct->setProduct($product);

        $orderProductFacade = $this->createOrderProductFacade();
        $orderProductFacade->addOrderProductsToStock([$orderProduct]);

        $this->assertSame($productStockQuantity, $product->getStockQuantity());
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\OrderProductFacade
     */
    private function createOrderProductFacade()
    {
        $moduleFacadeMock = $this->getMockBuilder(ModuleFacade::class)
            ->setMethods(['isEnabled'])
            ->disableOriginalConstructor()
            ->getMock();
        $moduleFacadeMock->expects($this->any())->method('isEnabled')->willReturn(true);

        return new OrderProductFacade(
            $this->createMock(EntityManager::class),
            $this->createMock(ProductHiddenRecalculator::class),
            $this->createMock(ProductSellingDeniedRecalculator::class),
            $this->createMock(ProductAvailabilityRecalculationScheduler::class),
            $this->createMock(ProductVisibilityFacade::class),
            $moduleFacadeMock
        );
    }
}
