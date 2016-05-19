<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order\Item;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderProductService;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

/**
 * @UglyTest
 */
class OrderProductServiceTest extends PHPUnit_Framework_TestCase {

	public function testSubtractOrderProductsFromStockUsingStock() {
		$productStockQuantity = 15;
		$orderProductQuantity = 10;

		$orderMock = $this->getMock(Order::class, [], [], '', false);

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

	public function testSubtractOrderProductsFromStockNotUsingStock() {
		$productStockQuantity = 15;
		$orderProductQuantity = 10;

		$orderMock = $this->getMock(Order::class, [], [], '', false);

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

	public function testAddOrderProductsToStockUsingStock() {
		$productStockQuantity = 15;
		$orderProductQuantity = 10;

		$orderMock = $this->getMock(Order::class, [], [], '', false);

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

	public function testAddOrderProductsToStockNotUsingStock() {
		$productStockQuantity = 15;
		$orderProductQuantity = 10;

		$orderMock = $this->getMock(Order::class, [], [], '', false);

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
