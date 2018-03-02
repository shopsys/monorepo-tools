<?php

namespace Shopsys\FrameworkBundle\Model\Category\Detail;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;

class CategoryDetailFactory
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @return \Shopsys\FrameworkBundle\Model\Category\Detail\CategoryDetail[]
     */
    public function createDetailsHierarchy(array $categories)
    {
        $firstLevelCategories = $this->getFirstLevelCategories($categories);
        $categoriesByParentId = $this->getCategoriesIndexedByParentId($categories);

        $categoryDetails = [];
        foreach ($firstLevelCategories as $firstLevelCategory) {
            $categoryDetails[] = new CategoryDetail(
                $firstLevelCategory,
                $this->getChildrenDetails($firstLevelCategory, $categoriesByParentId)
            );
        }

        return $categoryDetails;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]
     */
    public function createLazyLoadedDetails($categories, DomainConfig $domainConfig)
    {
        $categoriesWithChildren = $this->categoryRepository->getCategoriesWithVisibleChildren($categories, $domainConfig->getId());

        $lazyLoadedCategoryDetails = [];
        foreach ($categories as $category) {
            $hasChildren = in_array($category, $categoriesWithChildren, true);
            $lazyLoadedCategoryDetails[] = new LazyLoadedCategoryDetail(
                function () use ($category, $domainConfig) {
                    $categories = $this->categoryRepository->getTranslatedVisibleSubcategoriesByDomain($category, $domainConfig);

                    return $this->createLazyLoadedDetails($categories, $domainConfig);
                },
                $category,
                $hasChildren
            );
        }

        return $lazyLoadedCategoryDetails;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[][] $categoriesByParentId
     * @return \Shopsys\FrameworkBundle\Model\Category\Detail\CategoryDetail[]
     */
    private function getChildrenDetails(Category $category, array $categoriesByParentId)
    {
        if (!array_key_exists($category->getId(), $categoriesByParentId)) {
            return [];
        }

        $childDetails = [];

        foreach ($categoriesByParentId[$category->getId()] as $childCategory) {
            $childDetails[] = new CategoryDetail(
                $childCategory,
                $this->getChildrenDetails($childCategory, $categoriesByParentId)
            );
        }

        return $childDetails;
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
