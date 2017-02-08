<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\Item\CartItem;

class Cart {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	private $cartItems;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem[] $cartItems
	 */
	public function __construct(array $cartItems) {
		$this->cartItems = $cartItems;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem $item
	 */
	public function addItem(CartItem $item) {
		$this->cartItems[] = $item;
	}

	/**
	 * @param int $cartItemId
	 */
	public function removeItemById($cartItemId) {
		foreach ($this->cartItems as $key => $cartItem) {
			if ($cartItem->getId() === $cartItemId) {
				unset($this->cartItems[$key]);
				return;
			}
		}
		$message = 'Cart item with ID = ' . $cartItemId . ' is not in cart for remove.';
		throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException($message);
	}

	public function clean() {
		$this->cartItems = [];
	}

	/**
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem[]
	 */
	public function getItems() {
		return $this->cartItems;
	}

	/**
	 * @return int
	 */
	public function getItemsCount() {
		return count($this->getItems());
	}

	/**
	 * @return bool
	 */
	public function isEmpty() {
		return $this->getItemsCount() === 0;
	}

}
