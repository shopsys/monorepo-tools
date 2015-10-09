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
		$orderProduct = new OrderProduct($order, 'productName', 0, 0, 0, 1, null, null, null);
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
		$productItem = new OrderProduct($order, 'productName', 0, 0, 0, 1, null, null);
		$paymentItem = new OrderPayment($order, 'paymentName', 0, 0, 0, 1, $payment);
		$order->addItem($productItem);
		$order->addItem($paymentItem);

		$this->assertSame(1, $order->getProductItemsCount());
	}

	public function testOrderWithDeliveryAddressSameAsBillingAddress() {
		$orderData = new OrderData();
		$orderStatus = new OrderStatus(new OrderStatusData(), OrderStatus::TYPE_NEW);

		$orderData->companyName = 'companyName';
		$orderData->telephone = 'telephone';
		$orderData->firstName = 'firstName';
		$orderData->lastName = 'lastName';
		$orderData->street = 'street';
		$orderData->city = 'city';
		$orderData->postcode = 'postcode';
		$orderData->deliveryAddressSameAsBillingAddress = true;

		$order = new Order($orderData, 'orderNumber', $orderStatus, 'urlHash', null);

		$this->assertSame('companyName', $order->getDeliveryCompanyName());
		$this->assertSame('telephone', $order->getDeliveryTelephone());
		$this->assertSame('firstName lastName', $order->getDeliveryContactPerson());
		$this->assertSame('street', $order->getDeliveryStreet());
		$this->assertSame('city', $order->getDeliveryCity());
		$this->assertSame('postcode', $order->getDeliveryPostcode());
	}

	public function testOrderWithoutDeliveryAddressSameAsBillingAddress() {
		$orderData = new OrderData();
		$orderStatus = new OrderStatus(new OrderStatusData(), OrderStatus::TYPE_NEW);

		$orderData->companyName = 'companyName';
		$orderData->telephone = 'telephone';
		$orderData->firstName = 'firstName';
		$orderData->lastName = 'lastName';
		$orderData->street = 'street';
		$orderData->city = 'city';
		$orderData->postcode = 'postCode';
		$orderData->deliveryAddressSameAsBillingAddress = false;
		$orderData->deliveryCompanyName = 'deliveryCompanyName';
		$orderData->deliveryTelephone = 'deliveryTelephone';
		$orderData->deliveryContactPerson = 'deliveryContactPerson';
		$orderData->deliveryStreet = 'deliveryStreet';
		$orderData->deliveryCity = 'deliveryCity';
		$orderData->deliveryPostcode = 'deliveryPostcode';

		$order = new Order($orderData, 'orderNumber', $orderStatus, 'urlHash', null);

		$this->assertSame('deliveryCompanyName', $order->getDeliveryCompanyName());
		$this->assertSame('deliveryTelephone', $order->getDeliveryTelephone());
		$this->assertSame('deliveryContactPerson', $order->getDeliveryContactPerson());
		$this->assertSame('deliveryStreet', $order->getDeliveryStreet());
		$this->assertSame('deliveryCity', $order->getDeliveryCity());
		$this->assertSame('deliveryPostcode', $order->getDeliveryPostCode());
	}

}
