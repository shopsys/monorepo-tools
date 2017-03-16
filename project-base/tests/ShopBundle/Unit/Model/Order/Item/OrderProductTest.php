<?php

namespace Tests\ShopBundle\Unit\Model\Order\Item;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\Item\OrderProduct;
use Shopsys\ShopBundle\Model\Order\Order;
use Shopsys\ShopBundle\Model\Pricing\Price;
use Shopsys\ShopBundle\Model\Product\Product;
use Shopsys\ShopBundle\Model\Product\ProductData;

class OrderProductTest extends PHPUnit_Framework_TestCase
{
    public function testEditWithProduct()
    {
        $orderMock = $this->getMock(Order::class, [], [], '', false);
        $productMock = $this->getMock(Product::class, [], [], '', false);
        $productPrice = new Price(0, 0);

        $orderItemData = new OrderItemData();
        $orderItemData->name = 'newName';
        $orderItemData->priceWithVat = 20;
        $orderItemData->priceWithoutVat = 30;
        $orderItemData->quantity = 2;
        $orderItemData->vatPercent = 10;

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, 1, null, null, $productMock);
        $orderProduct->edit($orderItemData);

        $this->assertSame('newName', $orderProduct->getName());
        $this->assertSame(20, $orderProduct->getPriceWithVat());
        $this->assertSame(30, $orderProduct->getPriceWithoutVat());
        $this->assertSame(2, $orderProduct->getQuantity());
        $this->assertSame(10, $orderProduct->getvatPercent());
    }

    public function testEditWithoutProduct()
    {
        $orderMock = $this->getMock(Order::class, [], [], '', false);
        $productPrice = new Price(0, 0);

        $orderItemData = new OrderItemData();
        $orderItemData->name = 'newName';
        $orderItemData->priceWithVat = 20;
        $orderItemData->priceWithoutVat = 30;
        $orderItemData->quantity = 2;
        $orderItemData->vatPercent = 10;

        $orderProduct = new OrderProduct($orderMock, 'productName', $productPrice, 0, 1, null, null);
        $orderProduct->edit($orderItemData);

        $this->assertSame('newName', $orderProduct->getName());
        $this->assertSame(20, $orderProduct->getPriceWithVat());
        $this->assertSame(30, $orderProduct->getPriceWithoutVat());
        $this->assertSame(2, $orderProduct->getQuantity());
        $this->assertSame(10, $orderProduct->getvatPercent());
    }

    public function testConstructWithMainVariantThrowsException()
    {
        $variant = Product::create(new ProductData());
        $mainVariant = Product::createMainVariant(new ProductData(), [$variant]);
        $productPrice = new Price(0, 0);

        $orderMock = $this->getMock(Order::class, [], [], '', false);

        $this->setExpectedException(\Shopsys\ShopBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException::class);

        new OrderProduct($orderMock, 'productName', $productPrice, 0, 1, null, 'catnum', $mainVariant);
    }
}
