<?php

namespace Shopsys\FrameworkBundle\Model\Product\BestsellingProduct;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ManualBestsellingProductFactory implements ManualBestsellingProductFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    private $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

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
        $classData = $this->entityNameResolver->resolve(ManualBestsellingProduct::class);

        return new $classData($domainId, $category, $product, $position);
    }
}
