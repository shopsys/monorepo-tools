<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\CartItemRepository;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CartSingletonFactory {
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;
	
	/**
	 * @var \SS6\ShopBundle\Model\Customer\CartItemRepository $cartItemRepository
	 */
	private $cartItemRepository;
	
	/**
	 * @param \SS6\ShopBundle\Model\Customer\CartItemRepository $cartItemRepository
	 */
	public function __construct(CartItemRepository $cartItemRepository) {
		$this->cartItemRepository = $cartItemRepository;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @return \SS6\ShopBundle\Model\Cart\Cart
	 */
	public function get(CustomerIdentifier $customerIdentifier) {
		if ($this->cart === null) {
			$this->cart = $this->createNewCart($customerIdentifier);
		}
		return $this->cart;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @return \SS6\ShopBundle\Model\Cart\Cart
	 */
	private function createNewCart(CustomerIdentifier $customerIdentifier) {
		$cartItems = $this->cartItemRepository->findAllByCustomerIdentifier($customerIdentifier);
		$cart = new Cart($cartItems);
		return $cart;
	}

}
