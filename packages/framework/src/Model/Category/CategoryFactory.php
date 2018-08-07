<?php

namespace Shopsys\FrameworkBundle\Model\Category;

class CategoryFactory implements CategoryFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $data
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $data): Category
    {
        return new Category($data);
    }
}
