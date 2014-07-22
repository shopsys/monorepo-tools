<?php

namespace SS6\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Cart\CartSingletonFactory;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Customer\CustomerIdentifierFactory;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class CartMigrationFacade {

	const SESSION_PREVIOUS_ID = 'previous_id';
	
	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartService
	 */
	private $cartService;
	
	/**
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;
	
	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	private $customerIdentifier;
	
	/**
	 * 
	 * @var \SS6\ShopBundle\Model\Cart\CartSingletonFactory
	 */
	private $cartSingletonFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerIdentifierFactory
	 */
	private $customerIdentifierFactory;
	
	/**
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Cart\CartService $cartService
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Cart\CartSingletonFactory $cartSingletonFactory
	 */
	public function __construct(
		EntityManager $em,
		CartService $cartService,
		Cart $cart,
		CustomerIdentifier $customerIdentifier,
		CartSingletonFactory $cartSingletonFactory,
		CustomerIdentifierFactory $customerIdentifierFactory
	) {
		$this->em = $em;
		$this->cartService = $cartService;
		$this->cart = $cart;
		$this->customerIdentifier = $customerIdentifier;
		$this->cartSingletonFactory = $cartSingletonFactory;
		$this->customerIdentifierFactory = $customerIdentifierFactory;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	private function mergeCurrentCartWithCart(Cart $cart) {
		$this->cartService->mergeCarts($this->cart, $cart, $this->customerIdentifier);

		foreach ($cart->getItems() as $itemToRemove) {
			$this->em->remove($itemToRemove);
		}

		foreach ($this->cart->getItems() as $item) {
			$this->em->persist($item);
		}

		$this->em->flush();
	}

	/**
	 * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $filterControllerEvent
	 */
	public function onKernelController(FilterControllerEvent $filterControllerEvent) {
		$session = $filterControllerEvent->getRequest()->getSession();

		$previousId = $session->get(self::SESSION_PREVIOUS_ID);
		if (!empty($previousId) && $previousId !== $session->getId()) {
			$previousCustomerIdentifier = $this->customerIdentifierFactory->getOnlyWithSessionId($previousId);
			$cart = $this->cartSingletonFactory->get($previousCustomerIdentifier);
			$this->mergeCurrentCartWithCart($cart);
		}
		$session->set(self::SESSION_PREVIOUS_ID, $session->getId());
	}
}
