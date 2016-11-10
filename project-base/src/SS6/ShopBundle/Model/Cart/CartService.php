<?php

namespace SS6\ShopBundle\Model\Cart;

use SS6\ShopBundle\Model\Cart\Item\CartItem;
use SS6\ShopBundle\Model\Customer\CustomerIdentifier;
use SS6\ShopBundle\Model\Order\Item\QuantifiedProduct;
use SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use SS6\ShopBundle\Model\Product\Product;

class CartService {

	/**
	 * @var \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
	 */
	private $productPriceCalculation;

	/**
	 * @param \SS6\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
	 */
	public function __construct(ProductPriceCalculationForUser $productPriceCalculation) {
		$this->productPriceCalculation = $productPriceCalculation;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 * @param \SS6\ShopBundle\Model\Product\Product $product
	 * @param int $quantity
	 * @return \SS6\ShopBundle\Model\Cart\AddProductResult
	 */
	public function addProductToCart(Cart $cart, CustomerIdentifier $customerIdentifier, Product $product, $quantity) {
		if (!is_int($quantity) || $quantity <= 0) {
			throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
		}

		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getProduct() === $product) {
				$cartItem->changeQuantity($cartItem->getQuantity() + $quantity);
				return new AddProductResult($cartItem, false, $quantity);
			}
		}

		$productPrice = $this->productPriceCalculation->calculatePriceForCurrentUser($product);
		$newCartItem = new CartItem($customerIdentifier, $product, $quantity, $productPrice->getPriceWithVat());
		$cart->addItem($newCartItem);
		return new AddProductResult($newCartItem, true, $quantity);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param array $quantities CartItem.id => quantity
	 */
	public function changeQuantities(Cart $cart, array $quantities) {
		foreach ($cart->getItems() as $cartItem) {
			if (array_key_exists($cartItem->getId(), $quantities)) {
				$cartItem->changeQuantity($quantities[$cartItem->getId()]);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param int $cartItemId
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem
	 */
	public function getCartItemById(Cart $cart, $cartItemId) {
		foreach ($cart->getItems() as $cartItem) {
			if ($cartItem->getId() === $cartItemId) {
				return $cartItem;
			}
		}
		$message = 'CartItem with id = ' . $cartItemId . ' not found in cart.';
		throw new \SS6\ShopBundle\Model\Cart\Exception\InvalidCartItemException($message);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 */
	public function cleanCart(Cart $cart) {
		$cart->clean();
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $resultingCart
	 * @param \SS6\ShopBundle\Model\Cart\Cart $mergedCart
	 * @param \SS6\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
	 */
	public function mergeCarts(Cart $resultingCart, Cart $mergedCart, CustomerIdentifier $customerIdentifier) {
		foreach ($mergedCart->getItems() as $cartItem) {
			$similarCartItem = $this->findSimilarCartItemByCartItem($resultingCart, $cartItem);
			if ($similarCartItem instanceof CartItem) {
				$similarCartItem->changeQuantity($cartItem->getQuantity());
			} else {
				$newCartItem = new CartItem(
					$customerIdentifier,
					$cartItem->getProduct(),
					$cartItem->getQuantity(),
					$cartItem->getWatchedPrice()
				);
				$resultingCart->addItem($newCartItem);
			}
		}
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @param \SS6\ShopBundle\Model\Cart\Item\CartItem $cartItem
	 * @return \SS6\ShopBundle\Model\Cart\Item\CartItem|null
	 */
	private function findSimilarCartItemByCartItem(Cart $cart, CartItem $cartItem) {
		foreach ($cart->getItems() as $similarCartItem) {
			if ($similarCartItem->isSimilarItemAs($cartItem)) {
				return $similarCartItem;
			}
		}

		return null;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Cart\Cart $cart
	 * @return \SS6\ShopBundle\Model\Order\Item\QuantifiedProduct[cartItemId]
	 */
	public function getQuantifiedProducts(Cart $cart) {
		$quantifiedProducts = [];
		foreach ($cart->getItems() as $cartItem) {
			$quantifiedProducts[$cartItem->getId()] = new QuantifiedProduct($cartItem->getProduct(), $cartItem->getQuantity());
		}

		return $quantifiedProducts;
	}

}
