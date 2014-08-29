<?php

namespace SS6\ShopBundle\Model\Order\Review;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderReview {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	private $cartItems;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[]
	 */
	private $cartItemsPrices;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $transportPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $paymentPrice;

	/**
	 * @var string
	 */
	private $totalPriceWithoutVat;

	/**
	 * @var string
	 */
	private $totalPriceWithVat;

	/**
	 * @var string
	 */
	private $totalPriceVatAmount;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem[] $cartItems
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[] $cartItemsPrices
	 * @param \SS6\ShopBundle\Model\Transport\Transport $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price $transportPrice
	 * @param \SS6\ShopBundle\Model\Payment\Payment $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price $paymentPrice
	 * @param string $totalPriceWithoutVat
	 * @param string $totalPriceWithVat
	 * @param string $totalPriceVatAmount
	 */
	public function __construct(
		array $cartItems,
		array $cartItemsPrices,
		Transport $transport,
		Price $transportPrice,
		Payment $payment,
		Price $paymentPrice,
		$totalPriceWithoutVat,
		$totalPriceWithVat,
		$totalPriceVatAmount
	) {
		$this->cartItems = $cartItems;
		$this->cartItemsPrices = $cartItemsPrices;
		$this->transport = $transport;
		$this->transportPrice = $transportPrice;
		$this->payment = $payment;
		$this->paymentPrice = $paymentPrice;
		$this->totalPriceWithoutVat = $totalPriceWithoutVat;
		$this->totalPriceWithVat = $totalPriceWithVat;
		$this->totalPriceVatAmount = $totalPriceVatAmount;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getCartItems() {
		return $this->cartItems;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[]
	 */
	public function getCartItemsPrices() {
		return $this->cartItemsPrices;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getTransportPrice() {
		return $this->transportPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	public function getPaymentPrice() {
		return $this->paymentPrice;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithoutVat() {
		return $this->totalPriceWithoutVat;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceWithVat() {
		return $this->totalPriceWithVat;
	}

	/**
	 * @return string
	 */
	public function getTotalPriceVatAmount() {
		return $this->totalPriceVatAmount;
	}

}
