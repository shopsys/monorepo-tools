<?php

namespace Shopsys\FrameworkBundle\Model\Product;

interface ProductDomainFactoryInterface
{

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductDomain
     */
    public function create(Product $product, int $domainId): ProductDomain;
}
