<?php

namespace Shopsys\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Cart\CartFactory;
use Shopsys\ShopBundle\Model\Cart\CartService;
use Shopsys\ShopBundle\Model\Customer\CurrentCustomer;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory;
use Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade;
use Shopsys\ShopBundle\Model\Product\ProductRepository;

class CartFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \Shopsys\ShopBundle\Model\Cart\CartService
	 */
	private $cartService;

	/**
	 * @var \Shopsys\ShopBundle\Model\Cart\CartFactory
	 */
	private $cartFactory;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory
	 */
	private $customerIdentifierFactory;

	/**
	 * @var \Shopsys\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @var \Shopsys\ShopBundle\Model\Customer\CurrentCustomer
	 */
	private $currentCustomer;

	/**
	 * @var \Shopsys\ShopBundle\Model\Order\PromoCode\CurrentPromoCodeFacade
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \Shopsys\ShopBundle\Model\Cart\CartService $cartService
	 * @param \Shopsys\ShopBundle\Model\Cart\CartFactory $cartFactory
	 * @param \Shopsys\ShopBundle\Model\Product\ProductRepository $productRepository
	 * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifierFactory $customerIdentifierFactory
	 * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
	 * @param \Shopsys\ShopBundle\Model\Customer\CurrentCustomer $currentCustomer
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
	 * @return \Shopsys\ShopBundle\Model\Cart\AddProductResult
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
		/* @var $result \Shopsys\ShopBundle\Model\Cart\AddProductResult */

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
	 * @return \Shopsys\ShopBundle\Model\Product\Product
	 */
	public function getProductByCartItemId($cartItemId) {
		$cart = $this->getCartOfCurrentCustomer();

		return $this->cartService->getCartItemById($cart, $cartItemId)->getProduct();
	}

	public function cleanAdditionalData() {
		$this->currentPromoCodeFacade->removeEnteredPromoCode();
	}
	/**
	 * @return \Shopsys\ShopBundle\Model\Cart\Cart
	 */
	public function getCartOfCurrentCustomer() {
		$customerIdentifier = $this->customerIdentifierFactory->get();

		return $this->cartFactory->get($customerIdentifier);
	}

	/**
	 * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[cartItemId]
	 */
	public function getQuantifiedProductsOfCurrentCustomer() {
		$cart = $this->getCartOfCurrentCustomer();

		return $this->cartService->getQuantifiedProducts($cart);
	}

}
