<?php

namespace Shopsys\FrameworkBundle\Model\Cart\Item;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Product\Product;

class CartItemFactory implements CartItemFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Cart\Cart $cart
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $quantity
     * @param string $watchedPrice
     * @return \Shopsys\FrameworkBundle\Model\Cart\Item\CartItem
     */
    public function create(
        Cart $cart,
        Product $product,
        int $quantity,
        string $watchedPrice
    ): CartItem {
        $classData = $this->entityNameResolver->resolve(CartItem::class);

        return new $classData($cart, $product, $quantity, $watchedPrice);
    }
}
