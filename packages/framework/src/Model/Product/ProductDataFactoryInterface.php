<?php

namespace Shopsys\FrameworkBundle\Model\Product;

interface ProductDataFactoryInterface
{
    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function create(): ProductData;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductData
     */
    public function createFromProduct(Product $product): ProductData;
}
