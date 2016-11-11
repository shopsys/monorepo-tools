<?php

namespace SS6\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Customer\CurrentCustomer;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
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
	 * @var \SS6\ShopBundle\Model\Cart\Cart
	 */
	private $cart;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductRepository
	 */
	private $productRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Customer\CustomerIdentifier
	 */
	private $customerIdentifier;

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
	 */
	private $currentPromoCodeFacade;

	public function __construct(
		EntityManager $em,
		CartService $cartService,
		Cart $cart,
		ProductRepository $productRepository,
		CustomerIdentifier $customerIdentifier,
		Domain $domain,
		CurrentCustomer $currentCustomer,
		CurrentPromoCodeFacade $currentPromoCodeFacade
	) {
		$this->em = $em;
		$this->cartService = $cartService;
		$this->cart = $cart;
		$this->productRepository = $productRepository;
		$this->customerIdentifier = $customerIdentifier;
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
		$result = $this->cartService->addProductToCart($this->cart, $this->customerIdentifier, $product, $quantity);
		/* @var $result \SS6\ShopBundle\Model\Cart\AddProductResult */

		$this->em->persist($result->getCartItem());
		$this->em->flush();

		return $result;
	}

	/**
	 * @param array $quantities CartItem.id => quantity
	 */
	public function changeQuantities(array $quantities) {
		$this->cartService->changeQuantities($this->cart, $quantities);
		$this->em->flush();
	}

	/**
	 * @param int $cartItemId
	 */
	public function deleteCartItem($cartItemId) {
		$cartItemToDelete = $this->cartService->getCartItemById($this->cart, $cartItemId);
		$this->cart->removeItemById($cartItemId);
		$this->em->remove($cartItemToDelete);
		$this->em->flush();
	}

	public function cleanCart() {
		$cartItemsToDelete = $this->cart->getItems();
		$this->cartService->cleanCart($this->cart);

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
		return $this->cartService->getCartItemById($this->cart, $cartItemId)->getProduct();
	}

	public function cleanAdditionalData() {
		$this->currentPromoCodeFacade->removeEnteredPromoCode();
	}
}
