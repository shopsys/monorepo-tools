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
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItem
	 */
	private $cartItem;

	/**
	 * @var \SS6\ShopBundle\Model\Pricing\Price
	 */
	private $productPrice;

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
		$this->cartItem = $cartItem;
		$this->productPrice = $this->productPriceCalculation->calculatePrice($cartItem->getProduct());

		$cartItemPrice = new CartItemPrice(
			$this->productPrice->getBasePriceWithoutVat(),
			$this->productPrice->getBasePriceWithVat(),
			$this->productPrice->getBasePriceVatAmount(),
			$this->getTotalPriceWithoutVat(),
			$this->getTotalPriceWithVat(),
			$this->getTotalPriceVatAmount()
		);

		return $cartItemPrice;
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithoutVat() {
		return $this->getTotalPriceWithVat() - $this->getTotalPriceVatAmount();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceWithVat() {
		return $this->productPrice->getBasePriceWithVat() * $this->cartItem->getQuantity();
	}

	/**
	 * @return string
	 */
	private function getTotalPriceVatAmount() {
		return $this->getTotalPriceWithVat() * $this->cartItem->getProduct()->getVat()->getCoefficient();
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
