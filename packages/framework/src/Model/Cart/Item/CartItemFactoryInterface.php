<?php

namespace Shopsys\FrameworkBundle\Model\Cart\Item;

use Shopsys\FrameworkBundle\Model\Cart\Cart;
use Shopsys\FrameworkBundle\Model\Product\Product;

interface CartItemFactoryInterface
{
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
    ): CartItem;
}
