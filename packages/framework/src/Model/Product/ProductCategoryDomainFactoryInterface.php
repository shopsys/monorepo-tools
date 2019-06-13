<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Category\Category;

interface ProductCategoryDomainFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain
     */
    public function create(
        Product $product,
        Category $category,
        int $domainId
    ): ProductCategoryDomain;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[][] $categoriesIndexedByDomainId
     * @return \Shopsys\FrameworkBundle\Model\Product\ProductCategoryDomain[]
     */
    public function createMultiple(
        Product $product,
        array $categoriesIndexedByDomainId
    ): array;
}
