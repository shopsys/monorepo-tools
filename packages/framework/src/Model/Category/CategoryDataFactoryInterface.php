<?php

namespace Shopsys\FrameworkBundle\Model\Category;

interface CategoryDataFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    public function createFromCategory(Category $category): CategoryData;

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryData
     */
    public function create(): CategoryData;
}
