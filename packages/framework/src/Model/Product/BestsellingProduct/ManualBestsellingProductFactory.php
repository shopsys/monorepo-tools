<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ManualBestsellingProductFactory implements ManualBestsellingProductFactoryInterface
{
    /**
     * @param int $domainId
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $position
     * @return \Shopsys\FrameworkBundle\Model\Product\BestsellingProduct\ManualBestsellingProduct
     */
    public function create(
        int $domainId,
        Category $category,
        Product $product,
        int $position
    ): ManualBestsellingProduct {
        return new ManualBestsellingProduct($domainId, $category, $product, $position);
    }
}
