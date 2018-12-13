<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;

class CategoryFactory implements CategoryFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $data
     * @param \Shopsys\FrameworkBundle\Model\Category\Category|null $rootCategory
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $data, ?Category $rootCategory): Category
    {
        $classData = $this->entityNameResolver->resolve(Category::class);
        $category = new $classData($data);

        if ($rootCategory !== null && $category->getParent() === null) {
            $category->setParent($rootCategory);
        }

        return $category;
    }
}
