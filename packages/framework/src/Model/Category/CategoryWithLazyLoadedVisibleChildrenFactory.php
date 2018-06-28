<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;

class CategoryWithLazyLoadedVisibleChildrenFactory
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
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithLazyLoadedVisibleChildren[]
     */
    public function createCategoriesWithLazyLoadedVisibleChildren($categories, DomainConfig $domainConfig)
    {
        $categoriesWithVisibleChildren = $this->categoryRepository->getCategoriesWithVisibleChildren($categories, $domainConfig->getId());

        $categoriesWithLazyLoadedVisibleChildren = [];
        foreach ($categories as $category) {
            $hasChildren = in_array($category, $categoriesWithVisibleChildren, true);
            $categoriesWithLazyLoadedVisibleChildren[] = new CategoryWithLazyLoadedVisibleChildren(
                function () use ($category, $domainConfig) {
                    $categories = $this->categoryRepository->getTranslatedVisibleSubcategoriesByDomain($category, $domainConfig);

                    return $this->createCategoriesWithLazyLoadedVisibleChildren($categories, $domainConfig);
                },
                $category,
                $hasChildren
            );
        }

        return $categoriesWithLazyLoadedVisibleChildren;
    }
}
