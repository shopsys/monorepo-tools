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

}
