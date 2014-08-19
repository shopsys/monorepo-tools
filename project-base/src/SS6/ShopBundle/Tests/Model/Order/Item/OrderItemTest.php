<?php

namespace SS6\ShopBundle\Tests\Model\Order\Item;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Order\Order;
use SS6\ShopBundle\Model\Order\Item\OrderProduct;
use SS6\ShopBundle\Model\Order\Status\OrderStatus;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderItemTest extends PHPUnit_Framework_TestCase {
	
	public function testTotalPrice() {
		$number = '123456';
		$transport = new Transport('TransportName', 0);
		$payment = new Payment('PaymentName', 0);
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

		$productPrice1 = 1000;
		$productPrice2 = 10000;
		$productQuantity1 = 1;
		$productQuantity2 = 2;

		$product1 = new Product(new ProductData('ProductName1', null, null, null, null, $productPrice1));
		$product2 = new Product(new ProductData('ProductName2', null, null, null, null, $productPrice2));

		$orderProduct1 = new OrderProduct($order, $product1->getName(), $product1->getPrice(), $productQuantity1, $product1);
		$orderProduct2 = new OrderProduct($order, $product2->getName(), $product2->getPrice(), $productQuantity2, $product2);
		
		$this->assertEquals($productPrice1 * $productQuantity1, $orderProduct1->getTotalPrice());
		$this->assertEquals($productPrice2 * $productQuantity2, $orderProduct2->getTotalPrice());
	}
}
