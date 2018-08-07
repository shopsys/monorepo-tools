<?php

namespace Shopsys\FrameworkBundle\Model\Product\TopProduct;

use Shopsys\FrameworkBundle\Model\Product\Product;

interface TopProductFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @param int $position
     * @return \Shopsys\FrameworkBundle\Model\Product\TopProduct\TopProduct
     */
    public function create(
        Product $product,
        int $domainId,
        int $position
    ): TopProduct;
}
