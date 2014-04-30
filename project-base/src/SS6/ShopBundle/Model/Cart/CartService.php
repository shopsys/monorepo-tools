<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Product\Product;

class CartService {

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @param \SS6\ShopBundle\Model\Product\Product\Product $product
	 * @param int $quantity
	 * @return \SS6\ShopBundle\Model\Cart\AddProductResult
	 * @throws \SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException
	 */
	public function addProductToCart(Cart $cart, CustomerIdentifier $customerIdentifier, Product $product, $quantity) {
		if (!is_int($quantity) || $quantity <= 0) {
			throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
		}

		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getProduct() === $product) {
				$cartItem->changeQuantity($cartItem->getQuantity() + $quantity);
				return new AddProductResult($cartItem, false, $quantity);
			}
		}

		$newCartItem = new CartItem($customerIdentifier, $product, $quantity);
		return new AddProductResult($newCartItem, true, $quantity);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param array $quantities CartItem.id => quantity
	 */
	public function changeQuantities(Cart $cart, array $quantities) {
		foreach ($cart->getItems() as $cartItem) {
			if (array_key_exists($cartItem->getId(), $quantities)) {
				$cartItem->changeQuantity($quantities[$cartItem->getId()]);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param int $cartItemId
	 * @return \SS6\ShopBundle\Model\Cart\CartItem
	 */
	public function getCartItemById(Cart $cart, $cartItemId) {
		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getId() === $cartItemId) {
				return $cartItem;
			}
		}
		$message = 'CartItem with id = ' . $cartItemId . ' not found in cart.';
		throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException($message);
	}

}
