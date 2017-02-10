<?php

namespace Shopsys\ShopBundle\Model\Order\Item;

use Shopsys\ShopBundle\Model\Product\Product;

class QuantifiedProduct
{

    /**
     * @var \Shopsys\ShopBundle\Model\Product\Product
     */
    private $product;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @param \Shopsys\ShopBundle\Model\Product\Product $product
     * @param int $quantity
     */
    public function __construct(Product $product, $quantity) {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\Product\Product $product
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

}
