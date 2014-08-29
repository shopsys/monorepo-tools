<?php

namespace SS6\ShopBundle\Model\Cart\Item;

use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Cart\Item\CartItemPrice;
use SS6\ShopBundle\Model\Product\PriceCalculation as ProductPriceCalculation;

class PriceCalculation {

	/**
	 * @var \SS6\ShopBundle\Model\Product\PriceCalculation
	 */
	private $productPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Product\PriceCalculation $productPriceCalculation
	 */
	public function __construct(ProductPriceCalculation $productPriceCalculation) {
		$this->productPriceCalculation = $productPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem $cartItem
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice
	 */
	public function calculatePrice(CartItem $cartItem) {
		$productPrice = $this->productPriceCalculation->calculatePrice($cartItem->getProduct());

		$cartItemPrice = new CartItemPrice(
			$productPrice->getBasePriceWithoutVat(),
			$productPrice->getBasePriceWithVat(),
			$productPrice->getBasePriceVatAmount(),
			$productPrice->getBasePriceWithoutVat() * $cartItem->getQuantity(),
			$productPrice->getBasePriceWithVat() * $cartItem->getQuantity(),
			$productPrice->getBasePriceVatAmount() * $cartItem->getQuantity()
		);

		return $cartItemPrice;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem[] $cartItems
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItemPrice[] array indices are preserved
	 */
	public function calculatePrices(array $cartItems) {
		$cartItemPrices = array();
		foreach ($cartItems as $key => $cartItem) {
			$cartItemPrices[$key] = $this->calculatePrice($cartItem);
		}

		return $cartItemPrices;
	}

}
