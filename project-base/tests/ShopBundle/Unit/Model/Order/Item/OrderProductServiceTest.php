<?php

namespace Tests\ShopBundle\Unit\Model\Order\Item;

use PHPUnit_Framework_TestCase;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProductService;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class OrderProductServiceTest extends PHPUnit_Framework_TestCase
{
    public function testSubtractOrderProductsFromStockUsingStock()
    {
        $productStockQuantity = 15;
        $orderProductQuantity = 10;

        $orderMock = $this->createMock(Order::class);

        $productData = new ProductData();
        $productData->usingStock = true;
        $productData->stockQuantity = $productStockQuantity;
        $product = Product::create($productData);
        $productPrice = new Price(0, 0);

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, null, null, $product);

        $orderProductService = new OrderProductService();
        $orderProductService->subtractOrderProductsFromStock([$orderProduct]);

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
        $product = Product::create($productData);
        $productPrice = new Price(0, 0);

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, null, null, $product);

        $orderProductService = new OrderProductService();
        $orderProductService->subtractOrderProductsFromStock([$orderProduct]);

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
        $product = Product::create($productData);
        $productPrice = new Price(0, 0);

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, null, null, $product);

        $orderProductService = new OrderProductService();
        $orderProductService->returnOrderProductsToStock([$orderProduct]);

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
        $product = Product::create($productData);
        $productPrice = new Price(0, 0);

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, $orderProductQuantity, null, null, $product);

        $orderProductService = new OrderProductService();
        $orderProductService->returnOrderProductsToStock([$orderProduct]);

        $this->assertSame($productStockQuantity, $product->getStockQuantity());
    }
}
