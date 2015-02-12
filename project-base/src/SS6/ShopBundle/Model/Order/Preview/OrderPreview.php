<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

class OrderPreview {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItem[cartItemIndex]
	 */
	private $cartItems;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[cartItemIndex]
	 */
	private $cartItemsPrices;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	private $transport;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	private $transportPrice;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\Payment|null
	 */
	private $payment;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price|null
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
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem[cartItemIndex] $cartItems
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[cartItemIndex] $cartItemsPrices
	 * @param string $totalPriceWithoutVat
	 * @param string $totalPriceWithVat
	 * @param string $totalPriceVatAmount
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 */
	public function __construct(
		array $cartItems,
		array $cartItemsPrices,
		$totalPriceWithoutVat,
		$totalPriceWithVat,
		$totalPriceVatAmount,
		Transport $transport = null,
		Price $transportPrice = null,
		Payment $payment = null,
		Price $paymentPrice = null
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
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[cartItemIndex]
	 */
	public function getCartItems() {
		return $this->cartItems;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[cartItemIndex]
	 */
	public function getCartItemsPrices() {
		return $this->cartItemsPrices;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Transport\Transport|null
	 */
	public function getTransport() {
		return $this->transport;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
	 */
	public function getTransportPrice() {
		return $this->transportPrice;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Payment\Payment|null
	 */
	public function getPayment() {
		return $this->payment;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Pricing\Price|null
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
