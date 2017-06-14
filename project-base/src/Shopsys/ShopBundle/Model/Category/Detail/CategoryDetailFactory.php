<?php

namespace Shopsys\ShopBundle\Model\Category\Detail;

use Shopsys\ShopBundle\Component\Domain\Config\DomainConfig;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;

class CategoryDetailFactory
{
    /**
     * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @return \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetail[]
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
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @param \Shopsys\ShopBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\ShopBundle\Model\Category\Detail\LazyLoadedCategoryDetail[]
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
                    $categoryDetails = $this->createLazyLoadedDetails($categories, $domainConfig);

                    return $categoryDetails;
                },
                $category,
                $hasChildren
            );
        }

        return $lazyLoadedCategoryDetails;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\Category\Category $category
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categoriesByParentId
     * @return \Shopsys\ShopBundle\Model\Category\Detail\CategoryDetail[]
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
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @return \Shopsys\ShopBundle\Model\Category\Category[]
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
     * @param \Shopsys\ShopBundle\Model\Category\Category[] $categories
     * @return \Shopsys\ShopBundle\Model\Category\Category[][]
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
