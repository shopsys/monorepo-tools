<?php

namespace Shopsys\ShopBundle\Model\Category;

use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;

class CategoryService {

    /**
     * @param \Shopsys\ShopBundle\Model\Category\CategoryData $categoryData
     * @param \Shopsys\ShopBundle\Model\Category\Category $rootCategory
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function create(CategoryData $categoryData, Category $rootCategory) {
        $category = new Category($categoryData);
        if ($category->getParent() === null) {
            $category->setParent($rootCategory);
        }

        return $category;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param \Shopsys\ShopBundle\Model\Category\CategoryData $categoryData
     * @param \Shopsys\ShopBundle\Model\Category\Category $rootCategory
     * @return \Shopsys\ShopBundle\Model\Category\Category
     */
    public function edit(Category $category, CategoryData $categoryData, Category $rootCategory) {
        $category->edit($categoryData);
        if ($category->getParent() === null) {
            $category->setParent($rootCategory);
        }

        return $category;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     */
    public function setChildrenAsSiblings(Category $category) {
        foreach ($category->getChildren() as $child) {
            $child->setParent($category->getParent());
        }
    }

}
