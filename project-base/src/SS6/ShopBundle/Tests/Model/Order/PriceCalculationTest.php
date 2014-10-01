<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Item\PriceCalculation as OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Order\PriceCalculation as OrderPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Price;

class PriceCalculationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @SuppressWarnings(PMD.ExcessiveMethodLength)
	 */
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

		$this->assertEquals(3220, $orderTotalPrice->getPriceWithVat());
		$this->assertEquals(1165, $orderTotalPrice->getPriceWithoutVat());
		$this->assertEquals(3200, $orderTotalPrice->getProductPrice());
	}
}
