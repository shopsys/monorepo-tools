<?php

namespace Shopsys\FrameworkBundle\Model\Product;

use Shopsys\FrameworkBundle\Model\Category\Category;

class ProductCategoryDomainFactory implements ProductCategoryDomainFactoryInterface
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
    ): ProductCategoryDomain {
        return new ProductCategoryDomain($product, $category, $domainId);
    }
}
