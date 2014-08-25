<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Item\OrderPayment;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Item\OrderTransport;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class OrderTest extends PHPUnit_Framework_TestCase {
	
	public function testTotalPrice() {
		$number = '123456';
		$transport = new Transport(new TransportData('TransportName', 199.95));
		$payment = new Payment(new PaymentData('PaymentName', 99.95));
		$orderStatus = new OrderStatus('StatusName', OrderStatus::TYPE_NEW);
		$firstName = 'FirstName';
		$lastName = 'LastName';
		$email = 'email@example.com';
		$telephone = '123456789';
		$street = 'Street';
		$city = 'City';
		$postcode = '12345';
		$order = new Order(
			$number,
			$transport,
			$payment,
			$orderStatus,
			$firstName,
			$lastName,
			$email,
			$telephone,
			$street,
			$city,
			$postcode
		);

		$vat1 = new Vat('vat', 21);
		$vat2 = new Vat('vat', 21);
		$product1 = new Product(new ProductData('ProductName1', null, null, null, null, 1000, $vat1));
		$product2 = new Product(new ProductData('ProductName2', null, null, null, null, 10000, $vat2));

		$orderProduct1 = new OrderProduct($order, $product1->getName(), $product1->getPrice(), 1, $product1);
		$orderProduct2 = new OrderProduct($order, $product2->getName(), $product2->getPrice(), 2, $product2);
		$orderPayment = new OrderPayment($order, $payment->getName(), $payment->getPrice(), 1, $payment);
		$orderTransport = new OrderTransport($order, $transport->getName(), $transport->getPrice(), 1, $transport);

		$this->assertEquals(199.95 + 99.95 + 1000 + 2 * 10000, $order->getTotalPrice());
		$this->assertEquals(1000 + 2 * 10000, $order->getTotalProductPrice());
	}
}
