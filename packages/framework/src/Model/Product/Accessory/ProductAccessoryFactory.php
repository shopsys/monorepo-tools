<?php

namespace Shopsys\FrameworkBundle\Model\Product\Accessory;

use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductAccessoryFactory implements ProductAccessoryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $accessory
     * @param int $position
     * @return \Shopsys\FrameworkBundle\Model\Product\Accessory\ProductAccessory
     */
    public function create(
        Product $product,
        Product $accessory,
        int $position
    ): ProductAccessory {
        return new ProductAccessory($product, $accessory, $position);
    }
}
