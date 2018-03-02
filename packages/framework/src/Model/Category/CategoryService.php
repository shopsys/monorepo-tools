<?php

namespace Shopsys\FrameworkBundle\Model\Category;

class CategoryService
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $rootCategory
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $categoryData, Category $rootCategory)
    {
        $category = new Category($categoryData);
        if ($category->getParent() === null) {
            $category->setParent($rootCategory);
        }

        return $category;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $rootCategory
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function edit(Category $category, CategoryData $categoryData, Category $rootCategory)
    {
        $category->edit($categoryData);
        if ($category->getParent() === null) {
            $category->setParent($rootCategory);
        }

        return $category;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     */
    public function setChildrenAsSiblings(Category $category)
    {
        foreach ($category->getChildren() as $child) {
            $child->setParent($category->getParent());
        }
    }
}
