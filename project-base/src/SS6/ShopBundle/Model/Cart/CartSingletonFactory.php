<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\CartItemRepository;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CartSingletonFactory {
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartItemRepository $cartItemRepository
	 */
	private $cartItemRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade
	 */
	private $cartWatcherFacade;
	
	/**
	 * @param \SS6\ShopBundle\Model\Cart\CartItemRepository $cartItemRepository
	 * @param \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade $cartWatcherFacade
	 */
	public function __construct(CartItemRepository $cartItemRepository, CartWatcherFacade $cartWatcherFacade) {
		$this->cartItemRepository = $cartItemRepository;
		$this->cartWatcherFacade = $cartWatcherFacade;
	}
	
	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @return \SS6\ShopBundle\Model\Cart\Cart
	 */
	public function get(CustomerIdentifier $customerIdentifier) {
		if ($this->cart === null) {
			$this->cart = $this->createNewCart($customerIdentifier);
			$this->cartWatcherFacade->checkCartModifications($this->cart);
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
