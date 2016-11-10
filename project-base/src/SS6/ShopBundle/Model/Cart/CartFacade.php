<?php

namespace SS6\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Cart\CartFactory;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Customer\CustomerIdentifierFactory;
use SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use SS6\ShopBundle\Model\Product\ProductRepository;

class CartFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartService
	 */
	private $cartService;

	/**
	 * @var \SS6\ShopBundle\Model\Cart\CartFactory
	 */
	private $cartFactory;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerIdentifierFactory
	 */
	private $customerIdentifierFactory;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \SS6\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Cart\CartService $cartService
	 * @param \SS6\ShopBundle\Model\Cart\CartFactory $cartFactory
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 * @param \SS6\ShopBundle\Model\Customer\CurrentCustomer $currentCustomer
	 */
	private $currentPromoCodeFacade;

	public function __construct(
		EntityManager $em,
		CartService $cartService,
		CartFactory $cartFactory,
		ProductRepository $productRepository,
		CustomerIdentifierFactory $customerIdentifierFactory,
		Domain $domain,
		CurrentCustomer $currentCustomer,
		CurrentPromoCodeFacade $currentPromoCodeFacade
	) {
		$this->em = $em;
		$this->cartService = $cartService;
		$this->cartFactory = $cartFactory;
		$this->productRepository = $productRepository;
		$this->customerIdentifierFactory = $customerIdentifierFactory;
		$this->domain = $domain;
		$this->currentCustomer = $currentCustomer;
		$this->currentPromoCodeFacade = $currentPromoCodeFacade;
	}

	/**
	 * @param int $productId
	 * @param int $quantity
	 * @return \SS6\ShopBundle\Model\Cart\AddProductResult
	 */
	public function addProductToCart($productId, $quantity) {
		$product = $this->productRepository->getSellableById(
			$productId,
			$this->domain->getId(),
			$this->currentCustomer->getPricingGroup()
		);
		$customerIdentifier = $this->customerIdentifierFactory->get();
		$cart = $this->cartFactory->get($customerIdentifier);
		$result = $this->cartService->addProductToCart($cart, $customerIdentifier, $product, $quantity);
		/* @var $result \SS6\ShopBundle\Model\Cart\AddProductResult */

		$this->em->persist($result->getCartItem());
		$this->em->flush();

		return $result;
	}

	/**
	 * @param array $quantities CartItem.id => quantity
	 */
	public function changeQuantities(array $quantities) {
		$cart = $this->getCartOfCurrentCustomer();
		$this->cartService->changeQuantities($cart, $quantities);
		$this->em->flush();
	}

	/**
	 * @param int $cartItemId
	 */
	public function deleteCartItem($cartItemId) {
		$cart = $this->getCartOfCurrentCustomer();
		$cartItemToDelete = $this->cartService->getCartItemById($cart, $cartItemId);
		$cart->removeItemById($cartItemId);
		$this->em->remove($cartItemToDelete);
		$this->em->flush();
	}

	public function cleanCart() {
		$cart = $this->getCartOfCurrentCustomer();
		$cartItemsToDelete = $cart->getItems();
		$this->cartService->cleanCart($cart);

		foreach ($cartItemsToDelete as $cartItemToDelete) {
			$this->em->remove($cartItemToDelete);
		}

		$this->em->flush();

		$this->cleanAdditionalData();
	}

	/**
	 * @param int $cartItemId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProductByCartItemId($cartItemId) {
		$cart = $this->getCartOfCurrentCustomer();
		return $this->cartService->getCartItemById($cart, $cartItemId)->getProduct();
	}

	public function cleanAdditionalData() {
		$this->currentPromoCodeFacade->removeEnteredPromoCode();
	}
	/**
	 * @return \SS6\ShopBundle\Model\Cart\Cart
	 */
	public function getCartOfCurrentCustomer() {
		$customerIdentifier = $this->customerIdentifierFactory->get();

		return $this->cartFactory->get($customerIdentifier);
	}

}
