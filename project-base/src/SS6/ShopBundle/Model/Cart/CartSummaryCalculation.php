<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation;

class CartSummaryCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation
	 */
	private $cartItemPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItemPriceCalculation $cartItemPriceCalculation
	 */
	public function __construct(CartItemPriceCalculation $cartItemPriceCalculation) {
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
