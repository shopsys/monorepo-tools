<?php

namespace SS6\ShopBundle\Model\Cart;

class AddProductResult {
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartItem
	 */
	private $cartItem;
	
	/**
	 * @var bool
	 */
	private $isNew;
	
	/**
	 * @var int
	 */
	private $addedQuantity;
	
	/**
	 * @param \SS6\ShopBundle\Model\Cart\CartItem $cartItem
	 * @param bool $isNew
	 * @param int $addedQuantity
	 */
	public function __construct(CartItem $cartItem, $isNew, $addedQuantity) {
		$this->cartItem = $cartItem;
		$this->isNew = $isNew;
		$this->addedQuantity = $addedQuantity;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Cart\CartItem
	 */
	public function getCartItem() {
		return $this->cartItem;
	}

	/**
	 * 
	 * @return bool
	 */
	public function getIsNew() {
		return $this->isNew;
	}

	/**
	 * @return type
	 */
	public function getAddedQuantity() {
		return $this->addedQuantity;
	}
	
}
