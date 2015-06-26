<?php

namespace SS6\ShopBundle\Tests\Unit\Model\Order;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\OrderData;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Order\Status\OrderStatusData;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;

class OrderTest extends PHPUnit_Framework_TestCase {

	public function testGetProductItems() {
		$payment = new Payment(new PaymentData());
		$orderData = new OrderData();
		$orderStatus = new OrderStatus(new OrderStatusData(), OrderStatus::TYPE_NEW);

		$order = new Order($orderData, 'orderNumber', $orderStatus, 'urlHash', null);
		$orderProduct = new OrderProduct($order, 'productName', 0, 0, 0, 1, null);
		$orderPayment = new OrderPayment($order, 'paymentName', 0, 0, 0, 1, $payment);
		$order->addItem($orderProduct);
		$order->addItem($orderPayment);

		$productItems = $order->getProductItems();

		$this->assertCount(1, $productItems);
		$this->assertContainsOnlyInstancesOf(OrderProduct::class, $productItems);
	}

	public function testGetProductItemsCount() {
		$payment = new Payment(new PaymentData());
		$orderData = new OrderData();
		$orderStatus = new OrderStatus(new OrderStatusData(), OrderStatus::TYPE_NEW);

		$order = new Order($orderData, 'orderNumber', $orderStatus, 'urlHash', null);
		$productItem = new OrderProduct($order, 'productName', 0, 0, 0, 1, null);
		$paymentItem = new OrderPayment($order, 'paymentName', 0, 0, 0, 1, $payment);
		$order->addItem($productItem);
		$order->addItem($paymentItem);

		$this->assertSame(1, $order->getProductItemsCount());
	}

}
