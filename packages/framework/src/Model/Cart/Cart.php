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
    private $items;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $item
     */
    public function addItem(CartItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param int $itemId
     */
    public function removeItemById($itemId)
    {
        foreach ($this->items as $key => $item) {
            if ($item->getId() === $itemId) {
                unset($this->items[$key]);
                return;
            }
        }
        $message = 'Cart item with ID = ' . $itemId . ' is not in cart for remove.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    public function clean()
    {
        $this->items = [];
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem[]
     */
    public function getItems()
    {
        return $this->items;
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
     * @param array $quantitiesByItemId
     */
    public function changeQuantities(array $quantitiesByItemId)
    {
        foreach ($this->items as $item) {
            if (array_key_exists($item->getId(), $quantitiesByItemId)) {
                $item->changeQuantity($quantitiesByItemId[$item->getId()]);
            }
        }
    }

    /**
     * @param int $itemId
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function getItemById($itemId)
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $itemId) {
                return $item;
            }
        }
        $message = 'CartItem with id = ' . $itemId . ' not found in cart.';
        throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidCartItemException($message);
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Order\Item\QuantifiedProduct[]
     */
    public function getQuantifiedProductsIndexedByItemId()
    {
        $quantifiedProductsByItemId = [];
        foreach ($this->items as $item) {
            $quantifiedProductsByItemId[$item->getId()] = new QuantifiedProduct($item->getProduct(), $item->getQuantity());
        }

        return $quantifiedProductsByItemId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cartToMerge
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     */
    public function mergeWithCart(self $cartToMerge, CartItemFactoryInterface $cartItemFactory, CustomerIdentifier $customerIdentifier)
    {
        foreach ($cartToMerge->getItems() as $itemToMerge) {
            $similarItem = $this->findSimilarItemByItem($itemToMerge);
            if ($similarItem instanceof CartItem) {
                $similarItem->changeQuantity($similarItem->getQuantity() + $itemToMerge->getQuantity());
            } else {
                $newCartItem = $cartItemFactory->create(
                    $customerIdentifier,
                    $itemToMerge->getProduct(),
                    $itemToMerge->getQuantity(),
                    $itemToMerge->getWatchedPrice()
                );
                $this->addItem($newCartItem);
            }
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem $item
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem|null
     */
    protected function findSimilarItemByItem(CartItem $item)
    {
        foreach ($this->items as $similarItem) {
            if ($similarItem->isSimilarItemAs($item)) {
                return $similarItem;
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

        foreach ($this->items as $item) {
            if ($item->getProduct() === $product) {
                $item->changeQuantity($item->getQuantity() + $quantity);
                $item->changeAddedAt(new \DateTime());
                return new AddProductResult($item, false, $quantity);
            }
        }

        $productPrice = $productPriceCalculation->calculatePriceForCurrentUser($product);
        $newCartItem = $cartItemFactory->create($customerIdentifier, $product, $quantity, $productPrice->getPriceWithVat());
        $this->addItem($newCartItem);
        return new AddProductResult($newCartItem, true, $quantity);
    }
}
