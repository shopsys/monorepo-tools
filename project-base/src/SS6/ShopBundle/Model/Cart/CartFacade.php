<?php

namespace SS6\ShopBundle\Model\Cart;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
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
	 * @param \Doctrine\ORM\EntityManager $em
	 * @param \SS6\ShopBundle\Model\Cart\CartService $cartService
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Product\ProductRepository $productRepository
	 */
	public function __construct(EntityManager $em, CartService $cartService, Cart $cart, ProductRepository $productRepository, 
			CustomerIdentifier $customerIdentifier) {
		$this->em = $em;
		$this->cartService = $cartService;
		$this->cart = $cart;
		$this->productRepository = $productRepository;
		$this->customerIdentifier = $customerIdentifier;
	}

	/**
	 * @param int $productId
	 * @param int $quantity
	 */
	public function addProductToCart($productId, $quantity) {
		$product = $this->productRepository->getVisibleById($productId);
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
	}

	/**
	 * @param int $cartItemId
	 * @return \SS6\ShopBundle\Model\Product\Product
	 */
	public function getProductByIdCartItem($cartItemId) {
		return $this->cartService->getCartItemById($this->cart, $cartItemId)->getProduct();
	}
}
