<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class CategoryFactory implements CategoryFactoryInterface
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
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $data
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $data): Category
    {
        $classData = $this->entityNameResolver->resolve(Category::class);

        return new $classData($data);
    }
}
