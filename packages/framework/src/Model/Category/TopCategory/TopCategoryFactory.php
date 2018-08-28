<?php

namespace Shopsys\FrameworkBundle\Model\Category\TopCategory;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Category\Category;

class TopCategoryFactory implements TopCategoryFactoryInterface
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
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @param int $position
     * @return \Shopsys\FrameworkBundle\Model\Category\TopCategory\TopCategory
     */
    public function create(
        Category $category,
        int $domainId,
        int $position
    ): TopCategory {
        $classData = $this->entityNameResolver->resolve(TopCategory::class);

        return new $classData($category, $domainId, $position);
    }
}
