<?php

namespace SS6\ShopBundle\Tests\Model\Order;

use PHPUnit_Framework_TestCase;
use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Item\CartItemPrice;
use SS6\ShopBundle\Model\Order\Preview\OrderPreviewCalculation;
use SS6\ShopBundle\Model\Cart\Item\PriceCalculation as CartItemPriceCalculation;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PriceCalculation as PaymentPriceCalculation;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\PriceCalculation as TransportPriceCalculation;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderPreviewCalculationTest extends PHPUnit_Framework_TestCase {

	public function testCalculatePreviewWithTransportAndPayment() {
		$paymentPrice = new Price(100, 120, 20);
		$transportPrice = new Price(10, 12, 2);
		$cartItemPrice = new CartItemPrice(1000, 1200, 200, 2000, 2400, 400);
		$cartItemsPrices = [$cartItemPrice, $cartItemPrice];

		$cartItemPriceCalculationMock = $this->getMockBuilder(CartItemPriceCalculation::class)
			->setMethods(['calculatePrices', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$cartItemPriceCalculationMock->expects($this->once())->method('calculatePrices')->will($this->returnValue($cartItemsPrices));

		$paymentPriceCalculationMock = $this->getMockBuilder(PaymentPriceCalculation::class)
			->setMethods(['calculatePrice', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$paymentPriceCalculationMock->expects($this->once())->method('calculatePrice')->will($this->returnValue($paymentPrice));

		$transportPriceCalculationMock = $this->getMockBuilder(TransportPriceCalculation::class)
			->setMethods(['calculatePrice', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$transportPriceCalculationMock->expects($this->once())->method('calculatePrice')->will($this->returnValue($transportPrice));

		$previewCalculation = new OrderPreviewCalculation(
			$cartItemPriceCalculationMock,
			$transportPriceCalculationMock,
			$paymentPriceCalculationMock
		);

		$cartItemMock = $this->getMock(CartItem::class, [], [], '', false);
		$cartItems = [
			$cartItemMock,
			$cartItemMock
		];
		$cart = new Cart($cartItems);
		$transport = $this->getMock(Transport::class, [], [], '', false);
		$payment = $this->getMock(Payment::class, [], [], '', false);

		$orderPreview = $previewCalculation->calculatePreview($cart, $transport, $payment);

		$this->assertEquals($cartItems, $orderPreview->getCartItems());
		$this->assertEquals($cartItemsPrices, $orderPreview->getCartItemsPrices());
		$this->assertEquals($payment, $orderPreview->getPayment());
		$this->assertEquals($paymentPrice, $orderPreview->getPaymentPrice());
		$this->assertEquals(2 + 20 + 400 * 2, $orderPreview->getTotalPriceVatAmount());
		$this->assertEquals(12 + 120 + 2400 * 2, $orderPreview->getTotalPriceWithVat());
		$this->assertEquals(10 + 100 + 2000 * 2, $orderPreview->getTotalPriceWithoutVat());
		$this->assertEquals($transport, $orderPreview->getTransport());
		$this->assertEquals($transportPrice, $orderPreview->getTransportPrice());
	}

	public function testCalculatePreviewWithoutTransportAndPayment() {
		$cartItemPrice = new CartItemPrice(1000, 1200, 200, 2000, 2400, 400);
		$cartItemsPrices = [$cartItemPrice, $cartItemPrice];

		$cartItemPriceCalculationMock = $this->getMockBuilder(CartItemPriceCalculation::class)
			->setMethods(['calculatePrices', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$cartItemPriceCalculationMock->expects($this->once())->method('calculatePrices')->will($this->returnValue($cartItemsPrices));

		$paymentPriceCalculationMock = $this->getMockBuilder(PaymentPriceCalculation::class)
			->setMethods(['calculatePrice', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$paymentPriceCalculationMock->expects($this->never())->method('calculatePrice');

		$transportPriceCalculationMock = $this->getMockBuilder(TransportPriceCalculation::class)
			->setMethods(['calculatePrice', '__construct'])
			->disableOriginalConstructor()
			->getMock();
		$transportPriceCalculationMock->expects($this->never())->method('calculatePrice');

		$previewCalculation = new OrderPreviewCalculation(
			$cartItemPriceCalculationMock,
			$transportPriceCalculationMock,
			$paymentPriceCalculationMock
		);

		$cartItemMock = $this->getMock(CartItem::class, [], [], '', false);
		$cartItems = [
			$cartItemMock,
			$cartItemMock
		];
		$cart = new Cart($cartItems);
		
		$orderPreview = $previewCalculation->calculatePreview($cart, null, null);

		$this->assertEquals($cartItems, $orderPreview->getCartItems());
		$this->assertEquals($cartItemsPrices, $orderPreview->getCartItemsPrices());
		$this->assertNull($orderPreview->getPayment());
		$this->assertNull($orderPreview->getPaymentPrice());
		$this->assertEquals(400 * 2, $orderPreview->getTotalPriceVatAmount());
		$this->assertEquals(2400 * 2, $orderPreview->getTotalPriceWithVat());
		$this->assertEquals(2000 * 2, $orderPreview->getTotalPriceWithoutVat());
		$this->assertNull($orderPreview->getTransport());
		$this->assertNull($orderPreview->getTransportPrice());
	}
}
