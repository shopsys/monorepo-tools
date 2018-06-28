<?php

namespace Shopsys\FrameworkBundle\Model\Category;

class CategoryWithPreloadedChildrenFactory
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildren[]
     */
    public function createCategoriesWithPreloadedChildren(array $categories)
    {
        $firstLevelCategories = $this->getFirstLevelCategories($categories);
        $categoriesByParentId = $this->getCategoriesIndexedByParentId($categories);

        $categoriesWithPreloadedChildren = [];
        foreach ($firstLevelCategories as $firstLevelCategory) {
            $categoriesWithPreloadedChildren[] = new CategoryWithPreloadedChildren(
                $firstLevelCategory,
                $this->getCategoriesWithPreloadedChildren($firstLevelCategory, $categoriesByParentId)
            );
        }

        return $categoriesWithPreloadedChildren;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[][] $categoriesByParentId
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildren[]
     */
    private function getCategoriesWithPreloadedChildren(Category $category, array $categoriesByParentId)
    {
        if (!array_key_exists($category->getId(), $categoriesByParentId)) {
            return [];
        }

        $childCategoriesWithPreloadedChildren = [];

        foreach ($categoriesByParentId[$category->getId()] as $childCategory) {
            $childCategoriesWithPreloadedChildren[] = new CategoryWithPreloadedChildren(
                $childCategory,
                $this->getCategoriesWithPreloadedChildren($childCategory, $categoriesByParentId)
            );
        }

        return $childCategoriesWithPreloadedChildren;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    private function getFirstLevelCategories(array $categories)
    {
        $firstLevelCategories = [];

        foreach ($categories as $category) {
            if ($category->getLevel() === 1) {
                $firstLevelCategories[] = $category;
            }
        }

        return $firstLevelCategories;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[][]
     */
    private function getCategoriesIndexedByParentId(array $categories)
    {
        $categoriesIndexedByParentId = [];

        foreach ($categories as $category) {
            $parentId = $category->getParent()->getId();

            if ($parentId !== null) {
                if (!isset($categoriesIndexedByParentId[$parentId])) {
                    $categoriesIndexedByParentId[$parentId] = [];
                }

                $categoriesIndexedByParentId[$parentId][] = $category;
            }
        }

        return $categoriesIndexedByParentId;
    }
}
