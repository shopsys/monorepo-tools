<?php

namespace Shopsys\FrameworkBundle\Model\Product;

class ProductFactory implements ProductFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $data
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function create(ProductData $data): Product
    {
        return Product::create($data);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\ProductData $data
     * @param \Shopsys\FrameworkBundle\Model\Product\Product[] $variants
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function createMainVariant(ProductData $data, array $variants): Product
    {
        return Product::createMainVariant($data, $variants);
    }
}
