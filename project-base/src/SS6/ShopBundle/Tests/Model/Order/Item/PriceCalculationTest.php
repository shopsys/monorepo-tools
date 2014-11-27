<?php

namespace SS6\ShopBundle\Tests\Model\OrderItem;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Item\OrderItem;
use SS6\ShopBundle\Model\Order\Item\OrderItemData;
use SS6\ShopBundle\Model\Order\Item\OrderItemPriceCalculation;
use SS6\ShopBundle\Model\Pricing\PriceCalculation as GenericPriceCalculation;

class PriceCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePriceWithoutVat() {
		$genericPriceCalculationMock = $this->getMock(GenericPriceCalculation::class, ['getVatAmountByPriceWithVat'], [], '', false);
		$genericPriceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(100);

		$orderItemData = new OrderItemData();
		$orderItemData->setPriceWithVat(1000);
		$orderItemData->setVatPercent(10);

		$orderItemPriceCalculation = new OrderItemPriceCalculation($genericPriceCalculationMock);
		$orderItemPriceCalculation->calculatePriceWithoutVat($orderItemData);

		$this->assertEquals(round(1000 - 100, 6), round($orderItemData->getPriceWithoutVat(), 6));
	}

	public function testCalculateTotalPrice() {
		$genericPriceCalculationMock = $this->getMock(GenericPriceCalculation::class, ['getVatAmountByPriceWithVat'], [], '', false);
		$genericPriceCalculationMock->expects($this->once())->method('getVatAmountByPriceWithVat')->willReturn(10);

		$orderItemPriceCalculation = new OrderItemPriceCalculation($genericPriceCalculationMock);

		$orderItem = $this->getMockForAbstractClass(
			OrderItem::class, [], '', false, true, true,
			['getPriceWithVat', 'getQuantity', 'getVatPercent']
		);
		$orderItem->expects($this->once())->method('getPriceWithVat')->willReturn(100);
		$orderItem->expects($this->once())->method('getQuantity')->willReturn(2);
		$orderItem->expects($this->once())->method('getVatPercent')->willReturn(1);

		$totalPrice = $orderItemPriceCalculation->calculateTotalPrice($orderItem);

		$this->assertEquals(round(200, 6), round($totalPrice->getPriceWithVat(), 6));
		$this->assertEquals(round(190, 6), round($totalPrice->getPriceWithoutVat(), 6));
		$this->assertEquals(round(10, 6), round($totalPrice->getVatAmount(), 6));
	}
}
