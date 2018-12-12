<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use Shopsys\FrameworkBundle\Model\Cart\Item\CartItem;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

class Cart
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    private $cartItems;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[] $cartItems
     */
    public function __construct(array $cartItems)
    {
        $this->cartItems = $cartItems;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $item
     */
    public function addItem(CartItem $item)
    {
        $this->cartItems[] = $item;
    }

    /**
     * @param int $cartItemId
     */
    public function removeItemById($cartItemId)
    {
        foreach ($this->cartItems as $key => $cartItem) {
            if ($cartItem->getId() === $cartItemId) {
                unset($this->cartItems[$key]);
                return;
            }
        }
        $message = 'Cart item with ID = ' . $cartItemId . ' is not in cart for remove.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    public function clean()
    {
        $this->cartItems = [];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    public function getItems()
    {
        return $this->cartItems;
    }

    /**
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->getItems());
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->getItemsCount() === 0;
    }

    /**
     * @param array $quantitiesByCartItemId
     */
    public function changeQuantities(array $quantitiesByCartItemId)
    {
        foreach ($this->cartItems as $cartItem) {
            if (array_key_exists($cartItem->getId(), $quantitiesByCartItemId)) {
                $cartItem->changeQuantity($quantitiesByCartItemId[$cartItem->getId()]);
            }
        }
    }

    /**
     * @param int $cartItemId
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function getCartItemById($cartItemId)
    {
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getId() === $cartItemId) {
                return $cartItem;
            }
        }
        $message = 'CartItem with id = ' . $cartItemId . ' not found in cart.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProductsIndexedByCartItemId()
    {
        $quantifiedProductsByCartItemId = [];
        foreach ($this->cartItems as $cartItem) {
            $quantifiedProductsByCartItemId[$cartItem->getId()] = new QuantifiedProduct($cartItem->getProduct(), $cartItem->getQuantity());
        }

        return $quantifiedProductsByCartItemId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cartToMerge
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     */
    public function mergeWithCart(self $cartToMerge, CartItemFactoryInterface $cartItemFactory, CustomerIdentifier $customerIdentifier)
    {
        foreach ($cartToMerge->getItems() as $cartItemToMerge) {
            $similarCartItem = $this->findSimilarCartItemByCartItem($cartItemToMerge);
            if ($similarCartItem instanceof CartItem) {
                $similarCartItem->changeQuantity($similarCartItem->getQuantity() + $cartItemToMerge->getQuantity());
            } else {
                $newCartItem = $cartItemFactory->create(
                    $customerIdentifier,
                    $cartItemToMerge->getProduct(),
                    $cartItemToMerge->getQuantity(),
                    $cartItemToMerge->getWatchedPrice()
                );
                $this->addItem($newCartItem);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $cartItem
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem|null
     */
    protected function findSimilarCartItemByCartItem(CartItem $cartItem)
    {
        foreach ($this->cartItems as $similarCartItem) {
            if ($similarCartItem->isSimilarItemAs($cartItem)) {
                return $similarCartItem;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @return \Shopsys\FrameworkBundle\Model\Cart\AddProductResult
     */
    public function addProduct(
        CustomerIdentifier $customerIdentifier,
        Product $product,
        $quantity,
        ProductPriceCalculationForUser $productPriceCalculation,
        CartItemFactoryInterface $cartItemFactory
    ) {
        if (!is_int($quantity) || $quantity <= 0) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
        }

        foreach ($this->cartItems as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                $cartItem->changeQuantity($cartItem->getQuantity() + $quantity);
                $cartItem->changeAddedAt(new \DateTime());
                return new AddProductResult($cartItem, false, $quantity);
            }
        }

        $productPrice = $productPriceCalculation->calculatePriceForCurrentUser($product);
        $newCartItem = $cartItemFactory->create($customerIdentifier, $product, $quantity, $productPrice->getPriceWithVat());
        $this->addItem($newCartItem);
        return new AddProductResult($newCartItem, true, $quantity);
    }
}
