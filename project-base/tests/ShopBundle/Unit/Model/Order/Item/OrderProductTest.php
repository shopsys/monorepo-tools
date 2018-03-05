<?php

namespace Tests\ShopBundle\Unit\Model\Order\Item;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderItemData;
use Shopsys\FrameworkBundle\Model\Order\Item\OrderProduct;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Product;
use Shopsys\FrameworkBundle\Model\Product\ProductData;

class OrderProductTest extends TestCase
{
    public function testEditWithProduct()
    {
        $orderMock = $this->createMock(Order::class);
        $productMock = $this->createMock(Product::class);
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
        $orderMock = $this->createMock(Order::class);
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

        $orderMock = $this->createMock(Order::class);

        $this->expectException(\Shopsys\FrameworkBundle\Model\Order\Item\Exception\MainVariantCannotBeOrderedException::class);

        new OrderProduct($orderMock, 'productName', $productPrice, 0, 1, null, 'catnum', $mainVariant);
    }
}
