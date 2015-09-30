<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order\Item;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;

class OrderProductTest extends PHPUnit_Framework_TestCase {

	public function testEditWithProduct() {
		$orderMock = $this->getMock(Order::class, [], [], '', false);
		$productMock = $this->getMock(Product::class, [], [], '', false);

		$orderItemData = new OrderItemData();
		$orderItemData->name = 'newName';
		$orderItemData->priceWithVat = 20;
		$orderItemData->priceWithoutVat = 30;
		$orderItemData->quantity = 2;
		$orderItemData->vatPercent = 10;

		$orderProduct = new OrderProduct($orderMock, 'productName', 0, 0, 0, 1, null, $productMock);
		$orderProduct->edit($orderItemData);

		$this->assertSame('newName', $orderProduct->getName());
		$this->assertSame(20, $orderProduct->getPriceWithVat());
		$this->assertSame(30, $orderProduct->getPriceWithoutVat());
		$this->assertSame(2, $orderProduct->getQuantity());
		$this->assertSame(10, $orderProduct->getvatPercent());
	}

	public function testEditWithoutProduct() {
		$orderMock = $this->getMock(Order::class, [], [], '', false);

		$orderItemData = new OrderItemData();
		$orderItemData->name = 'newName';
		$orderItemData->priceWithVat = 20;
		$orderItemData->priceWithoutVat = 30;
		$orderItemData->quantity = 2;
		$orderItemData->vatPercent = 10;

		$orderProduct = new OrderProduct($orderMock, 'productName', 0, 0, 0, 1, null, null);
		$orderProduct->edit($orderItemData);

		$this->assertSame('newName', $orderProduct->getName());
		$this->assertSame(20, $orderProduct->getPriceWithVat());
		$this->assertSame(30, $orderProduct->getPriceWithoutVat());
		$this->assertSame(2, $orderProduct->getQuantity());
		$this->assertSame(10, $orderProduct->getvatPercent());
	}

	public function testConstructWithMainVariantThrowsException() {
		$mainVariant = new Product(new ProductData());
		$variant = new Product(new ProductData());

		$mainVariant->addVariant($variant);

		$orderMock = $this->getMock(Order::class, [], [], '', false);

		$this->setExpectedException(\SS6\ShopBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException::class);

		new OrderProduct($orderMock, 'productName', 0, 0, 0, 1, 'catnum', $mainVariant);
	}

}
