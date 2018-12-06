<?php

namespace Shopsys\FrameworkBundle\Model\Category;

class CategoryService
{
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
