<?php

namespace SS6\ShopBundle\Model\Order\Review;

use SS6\ShopBundle\Model\Cart\Cart;
use SS6\ShopBundle\Model\Payment\Payment;
use SS6\ShopBundle\Model\Pricing\Price;
use SS6\ShopBundle\Model\Transport\Transport;

use SS6\ShopBundle\Model\Cart\Item\PriceCalculation as CartItemPriceCalculation;
use SS6\ShopBundle\Model\Transport\PriceCalculation as TransportPriceCalculation;
use SS6\ShopBundle\Model\Payment\PriceCalculation as PaymentPriceCalculation;

class OrderReviewCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\PriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Transport\PriceCalculation
	 */
	private $transportPriceCalculation;

	/**
	 * @var \SS6\ShopBundle\Model\Payment\PriceCalculation
	 */
	private $paymentPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\PriceCalculation $cartItemPriceCalculation
	 * @param \SS6\ShopBundle\Model\Transport\PriceCalculation $transportPriceCalculation
	 * @param \SS6\ShopBundle\Model\Payment\PriceCalculation $paymentPriceCalculation
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
	 * @param \SS6\ShopBundle\Model\Order\Review\Cart $cart
	 * @param \SS6\ShopBundle\Model\Transport\Transport|null $transport
	 * @param \SS6\ShopBundle\Model\Payment\Payment|null $payment
	 * @return \SS6\ShopBundle\Model\Order\Review\OrderReview
	 */
	public function calculateReview(
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

		return new OrderReview(
			$cartItems,
			$cartItemsPrices,
			$totalPrice->getBasePriceWithoutVat(),
			$totalPrice->getBasePriceWithVat(),
			$totalPrice->getBasePriceVatAmount(),
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
			$totalPriceWithoutVat += $transportPrice->getBasePriceWithoutVat();
			$totalPriceWithVat += $transportPrice->getBasePriceWithVat();
			$totalPriceVatAmount += $transportPrice->getBasePriceVatAmount();
		}

		if ($paymentPrice !== null) {
			$totalPriceWithoutVat += $paymentPrice->getBasePriceWithoutVat();
			$totalPriceWithVat += $paymentPrice->getBasePriceWithVat();
			$totalPriceVatAmount += $paymentPrice->getBasePriceVatAmount();
		}

		return new Price(
			$totalPriceWithoutVat,
			$totalPriceWithVat,
			$totalPriceVatAmount
		);
	}

}
