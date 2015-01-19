<?php

namespace SS6\ShopBundle\Model\Order\Preview;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Payment\PaymentPriceCalculation;

use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;
use SS6\ShopBundle\Model\Transport\TransportPriceCalculation;

class OrderPreviewCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\TransportPriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation $cartItemPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\TransportPriceCalculation $transportPriceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PaymentPriceCalculation $paymentPriceCalculation
	 */
	public function __construct(
		CartItemPriceCalculation $cartItemPriceCalculation,
		TransportPriceCalculation $transportPriceCalculation,
		PaymentPriceCalculation $paymentPriceCalculation
	) {
		$this->cartItemPriceCalculation = $cartItemPriceCalculation;
		$this->transportPriceCalculation = $transportPriceCalculation;
		$this->paymentPriceCalculation = $paymentPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Order\Preview\Cart $cart
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @return \SS6\ShopBundle\Model\Order\Preview\OrderPreview
	 */
	public function calculatePreview(
		Cart $cart,
		Transport $transport = null,
		Payment $payment = null
	) {
		$cartItems = $cart->getItems();
		$cartItemsPrices = $this->cartItemPriceCalculation->calculatePrices($cartItems);

		if ($transport !== null) {
			$transportPrice = $this->transportPriceCalculation->calculatePrice($transport);
		} else {
			$transportPrice = null;
		}

		if ($payment !== null) {
			$paymentPrice = $this->paymentPriceCalculation->calculatePrice($payment);
		} else {
			$paymentPrice = null;
		}

		$totalPrice = $this->calculateTotalPrice(
			$cartItemsPrices,
			$transportPrice,
			$paymentPrice
		);

		return new OrderPreview(
			$cartItems,
			$cartItemsPrices,
			$totalPrice->getPriceWithoutVat(),
			$totalPrice->getPriceWithVat(),
			$totalPrice->getVatAmount(),
			$transport,
			$transportPrice,
			$payment,
			$paymentPrice
		);
	}

	/**
	 * @param array $cartItemsPrices
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $transportPrice
	 * @param \SS6\ShopBundle\Model\Pricing\Price|null $paymentPrice
	 * @return \SS6\ShopBundle\Model\Pricing\Price
	 */
	private function calculateTotalPrice(
		array $cartItemsPrices,
		Price $transportPrice = null,
		Price $paymentPrice = null
	) {
		$totalPriceWithoutVat = 0;
		$totalPriceWithVat = 0;
		$totalPriceVatAmount = 0;

		foreach ($cartItemsPrices as $cartItemsPrice) {
			/* @var $cartItemsPrice \SS6\ShopBundle\Model\Cart\Item\CartItemPrice */
			$totalPriceWithoutVat += $cartItemsPrice->getTotalPriceWithoutVat();
			$totalPriceWithVat += $cartItemsPrice->getTotalPriceWithVat();
			$totalPriceVatAmount += $cartItemsPrice->getTotalPriceVatAmount();
		}

		if ($transportPrice !== null) {
			$totalPriceWithoutVat += $transportPrice->getPriceWithoutVat();
			$totalPriceWithVat += $transportPrice->getPriceWithVat();
			$totalPriceVatAmount += $transportPrice->getVatAmount();
		}

		if ($paymentPrice !== null) {
			$totalPriceWithoutVat += $paymentPrice->getPriceWithoutVat();
			$totalPriceWithVat += $paymentPrice->getPriceWithVat();
			$totalPriceVatAmount += $paymentPrice->getVatAmount();
		}

		return new Price(
			$totalPriceWithoutVat,
			$totalPriceWithVat,
			$totalPriceVatAmount
		);
	}

}
