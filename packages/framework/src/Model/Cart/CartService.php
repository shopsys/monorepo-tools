<?php

namespace Shopsys\FrameworkBundle\Model\Cart;

use DateTime;
use Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface;
use Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

class CartService
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser
     */
    private $productPriceCalculation;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface
     */
    protected $cartItemFactory;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser $productPriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Cart\Item\CartItemFactoryInterface $cartItemFactory
     */
    public function __construct(
        ProductPriceCalculationForUser $productPriceCalculation,
        CartItemFactoryInterface $cartItemFactory
    ) {
        $this->productPriceCalculation = $productPriceCalculation;
        $this->cartItemFactory = $cartItemFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @param \Shopsys\FrameworkBundle\Model\Customer\CustomerIdentifier $customerIdentifier
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @return \Shopsys\FrameworkBundle\Model\Cart\AddProductResult
     */
    public function addProductToCart(Cart $cart, CustomerIdentifier $customerIdentifier, Product $product, $quantity)
    {
        if (!is_int($quantity) || $quantity <= 0) {
            throw new \Shopsys\FrameworkBundle\Model\Cart\Exception\InvalidQuantityException($quantity);
        }

        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->getProduct() === $product) {
                $cartItem->changeQuantity($cartItem->getQuantity() + $quantity);
                $cartItem->changeAddedAt(new DateTime());
                return new AddProductResult($cartItem, false, $quantity);
            }
        }

        $productPrice = $this->productPriceCalculation->calculatePriceForCurrentUser($product);
        $newCartItem = $this->cartItemFactory->create($customerIdentifier, $product, $quantity, $productPrice->getPriceWithVat());
        $cart->addItem($newCartItem);
        return new AddProductResult($newCartItem, true, $quantity);
    }
}
