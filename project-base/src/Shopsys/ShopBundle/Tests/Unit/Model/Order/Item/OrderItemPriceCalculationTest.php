<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\OrderItem;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Order\Item\OrderItem;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemData;
use Shopsys\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use Shopsys\ShopBundle\Model\Pricing\PriceCalculation;

class OrderItemPriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceWithoutVat() {
		$priceCalculationMock = $this->getMock(PriceCalculation::class, ['getVatAmountByPriceWithVat'], [], '', false);
		$priceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(100);

		$orderItemData = new OrderItemData();
		$orderItemData->priceWithVat = 1000;
		$orderItemData->vatPercent = 10;

		$orderItemPriceCalculation = new OrderItemPriceCalculation($priceCalculationMock);
		$priceWithoutVat = $orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);

		$this->assertSame(round(1000 - 100, 6), round($priceWithoutVat, 6));
	}

	public function testCalculateTotalPrice() {
		$priceCalculationMock = $this->getMock(PriceCalculation::class, ['getVatAmountByPriceWithVat'], [], '', false);
		$priceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(10);

		$orderItemPriceCalculation = new OrderItemPriceCalculation($priceCalculationMock);

		$orderItem = $this->getMockForAbstractClass(
			OrderItem::class, [], '', false, true, true,
			['getPriceWithVat', 'getQuantity', 'getVatPercent']
		);
		$orderItem->expects($this->once())->method('getPriceWithVat')->willReturn(100);
		$orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
		$orderItem->expects($this->once())->method('getVatPercent')->willReturn(1);

		$totalPrice = $orderItemPriceCalculation->calculateTotalPrice($orderItem);

		$this->assertSame(round(200, 6), round($totalPrice->getPriceWithVat(), 6));
		$this->assertSame(round(190, 6), round($totalPrice->getPriceWithoutVat(), 6));
		$this->assertSame(round(10, 6), round($totalPrice->getVatAmount(), 6));
	}
}
