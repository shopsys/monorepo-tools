<?php

namespace SS6\ShopBundle\Tests\Model\Order\Review;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Order\Review\OrderReview;
use SS6\ShopBundle\Model\Order\Review\OrderReviewItem;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentData;
use SS6\ShopBundle\Model\Pricing\Vat\Vat;
use SS6\ShopBundle\Model\Pricing\Vat\VatData;
use SS6\ShopBundle\Model\Product\Product;
use SS6\ShopBundle\Model\Product\ProductData;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportData;

class OrderReviewTest extends PHPUnit_Framework_TestCase {

	public function testGetTotalPrice() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat1 = new Vat(new VatData('vat', 21));
		$vat2 = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, 1000, $vat1));
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, 2000, $vat2));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);

		$cart = new Cart($cartItems);

		$transport = new Transport(new TransportData('Transport', 100));
		$payment = new Payment(new PaymentData('Payment', 50));

		$orderReviewPartial = new OrderReview($cart, null, null);
		$orderReview = new OrderReview($cart, $payment, $transport);

		$this->assertEquals(1000 + 2000 * 3, $orderReviewPartial->getTotalPrice());
		$this->assertEquals(1000 + 2000 * 3 + 100 + 50, $orderReview->getTotalPrice());
	}

	public function testGetItems() {
		$customerIdentifier = new CustomerIdentifier('randomString');

		$vat = new Vat(new VatData('vat', 21));
		$product1 = new Product(new ProductData('Product 1', null, null, null, null, 1000, $vat));
		$product2 = new Product(new ProductData('Product 2', null, null, null, null, 2000, $vat));

		$cartItem1 = new CartItem($customerIdentifier, $product1, 1);
		$cartItem2 = new CartItem($customerIdentifier, $product2, 3);
		$cartItems = array($cartItem1, $cartItem2);

		$cart = new Cart($cartItems);

		$transport = new Transport(new TransportData('Transport', 100));
		$payment = new Payment(new PaymentData('Payment', 50));

		$orderReview = new OrderReview($cart, $payment, $transport);

		$position = 0;
		foreach ($orderReview->getItems() as $item) {
			if ($position === 0) {
				$this->assertEquals($cartItem1->getQuantity(), $item->getQuantity(), 'Product quantity');
				$this->assertEquals($cartItem1->getTotalPrice(), $item->getPrice(), 'Product total price');
				$this->assertEquals(OrderReviewItem::TYPE_PRODUCT, $item->getType(), 'Item type');
			} elseif ($position === 1) {
				$this->assertEquals($cartItem2->getQuantity(), $item->getQuantity(), 'Product quantity');
				$this->assertEquals($cartItem2->getTotalPrice(), $item->getPrice(), 'Product total price');
				$this->assertEquals(OrderReviewItem::TYPE_PRODUCT, $item->getType(), 'Item type');
			} elseif ($position === 2) {
				$this->assertEquals($transport->getPrice(), $item->getPrice(), 'Transport price');
				$this->assertEquals(OrderReviewItem::TYPE_TRANSPORT, $item->getType(), 'Item type');
			} elseif ($position === 3) {
				$this->assertEquals($payment->getPrice(), $item->getPrice(), 'Payment price');
				$this->assertEquals(OrderReviewItem::TYPE_PAYMENT, $item->getType(), 'Item type');
			} else {
				$this->fail('Unknown item in order review');
			}
			$position++;
		}
	}


}
