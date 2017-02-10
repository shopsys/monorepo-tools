<?php

namespace Shopsys\ShopBundle\Model\Cart;

use Shopsys\ShopBundle\Model\Cart\Item\CartItem;
use Shopsys\ShopBundle\Model\Customer\CustomerIdentifier;
use Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\ShopBundle\Model\Product\Product;

class CartService
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculation;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     */
    public function __construct(ProductPriceCalculationForUser $productPriceCalculation) {
        $this->productPriceCalculation = $productPriceCalculation;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $quantity
     * @return \Shopsys\ShopBundle\Model\Cart\AddProductResult
     */
    public function addProductToCart(Cart $cart, CustomerIdentifier $customerIdentifier, Product $product, $quantity) {
        if (!is_int($quantity) || $quantity <= 0) {
            throw new \Shopsys\ShopBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
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
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
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
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @param int $cartItemId
     * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem
     */
    public function getCartItemById(Cart $cart, $cartItemId) {
        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getId() === $cartItemId) {
                return $cartItem;
            }
        }
        $message = 'CartItem with id = ' . $cartItemId . ' not found in cart.';
        throw new \Shopsys\ShopBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     */
    public function cleanCart(Cart $cart) {
        $cart->clean();
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $resultingCart
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $mergedCart
     * @param \Shopsys\ShopBundle\Model\Customer\CustomerIdentifier $customerIdentifier
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
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @param \Shopsys\ShopBundle\Model\Cart\Item\CartItem $cartItem
     * @return \Shopsys\ShopBundle\Model\Cart\Item\CartItem|null
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
     * @param \Shopsys\ShopBundle\Model\Cart\Cart $cart
     * @return \Shopsys\ShopBundle\Model\Order\Item\QuantifiedProduct[cartItemId]
     */
    public function getQuantifiedProducts(Cart $cart) {
        $quantifiedProducts = [];
        foreach ($cart->getItems() as $cartItem) {
            $quantifiedProducts[$cartItem->getId()] = new QuantifiedProduct($cartItem->getProduct(), $cartItem->getQuantity());
        }

        return $quantifiedProducts;
    }

}
