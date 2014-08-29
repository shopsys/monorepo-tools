<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\Item\PriceCalculation;

class CartSummaryCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\PriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\PriceCalculation $cartItemPriceCalculation
	 */
	public function __construct(PriceCalculation $cartItemPriceCalculation) {
		$this->cartItemPriceCalculation = $cartItemPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Cart\CartSummary
	 */
	public function calculateSummary(Cart $cart) {
		$quantity = 0;
		$totalPriceWithoutVat = 0;
		$totalPriceWithVat = 0;

		foreach ($cart->getItems() as $cartItem) {
			$quantity += $cartItem->getQuantity();

			$cartItemPrice = $this->cartItemPriceCalculation->calculatePrice($cartItem);
			$totalPriceWithoutVat += $cartItemPrice->getTotalPriceWithoutVat();
			$totalPriceWithVat += $cartItemPrice->getTotalPriceWithVat();
		}

		$cartSummary = new CartSummary(
			$quantity,
			$totalPriceWithoutVat,
			$totalPriceWithVat
		);

		return $cartSummary;
	}

}
