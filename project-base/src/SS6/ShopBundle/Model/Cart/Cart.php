<?php

namespace SS6\ShopBundle\Model\Cart;

class Cart {
	
	/**
	 * @var string
	 */
	private $price;
	
	/**
	 * @var int
	 */
	private $quantity;
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartItem[]
	 */
	private $cartItems;
	
	/**
	 * @param \SS6\ShopBundle\Model\Cart\CartItem[] $cartItems
	 */
	public function __construct(array $cartItems) {
		$this->cartItems = $cartItems;
		$this->calcSummaryInfo();
	}
	
	private function calcSummaryInfo() {
		$this->quantity = 0;
		$this->price = 0;
		foreach ($this->cartItems as $cartItem) {
			$this->quantity += $cartItem->getQuantity();
			$this->price += $cartItem->getTotalPrice();
		}
	}
	
	/**
	 * @return \SS6\ShopBundle\Model\Cart\CartItem[]
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
	 * @return int
	 */
	public function getQuantity() {
		return $this->quantity;
	}
	
	/**
	 * @return string
	 */
	public function getPrice() {
		return $this->price;
	}

}
