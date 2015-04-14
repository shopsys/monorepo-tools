<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Price;

class OrderPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testGetOrderTotalPrice() {
		$orderItems = [
			$this->getMock(OrderProduct::class, [], [], '', false),
			$this->getMock(OrderProduct::class, [], [], '', false),
			$this->getMock(OrderPayment::class, [], [], '', false),
			$this->getMock(OrderTransport::class, [], [], '', false),
		];

		$pricesMap = [
			[$orderItems[0], new Price(150, 200, 50)],
			[$orderItems[1], new Price(1000, 3000, 2000)],
			[$orderItems[2], new Price(15, 20, 5)],
			[$orderItems[3], new Price(0, 0, 0)],
		];

		$orderItemPriceCalculationMock = $this->getMockBuilder(OrderItemPriceCalculation::class)
			->setMethods(['__construct', 'calculateTotalPrice'])
			->disableOriginalConstructor()
			->getMock();
		$orderItemPriceCalculationMock
			->expects($this->exactly(4))
			->method('calculateTotalPrice')
			->willReturnMap($pricesMap);

		$priceCalculation = new OrderPriceCalculation($orderItemPriceCalculationMock);

		$orderMock = $this->getMock(Order::class, ['__construct', 'getItems'], [], '', false);
		$orderMock->expects($this->once())->method('getItems')->willReturn($orderItems);

		$orderTotalPrice = $priceCalculation->getOrderTotalPrice($orderMock);

		$this->assertSame(3220, $orderTotalPrice->getPriceWithVat());
		$this->assertSame(1165, $orderTotalPrice->getPriceWithoutVat());
		$this->assertSame(3200, $orderTotalPrice->getProductPriceWithVat());
	}
}
