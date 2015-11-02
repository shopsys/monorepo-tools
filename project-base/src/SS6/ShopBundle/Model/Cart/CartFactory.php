<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\Item\CartItemRepository;
use SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;

class CartFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart[]
	 */
	private $carts = [];

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Item\CartItemRepository
	 */
	private $cartItemRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\Watcher\CartWatcherFacade
	 */
	private $cartWatcherFacade;

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItemRepository $cartItemRepository
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
		$customerIdentifierHash = $customerIdentifier->getObjectHash();
		if (!array_key_exists($customerIdentifierHash, $this->carts)) {
			$this->carts[$customerIdentifierHash] = $this->createNewCart($customerIdentifier);
		}

		$cart = $this->carts[$customerIdentifierHash];
		$this->cartWatcherFacade->checkCartModifications($cart);

		return $cart;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @return \SS6\ShopBundle\Model\Cart\Cart
	 */
	private function createNewCart(CustomerIdentifier $customerIdentifier) {
		$cartItems = $this->cartItemRepository->getAllByCustomerIdentifier($customerIdentifier);

		return new Cart($cartItems);
	}

}
