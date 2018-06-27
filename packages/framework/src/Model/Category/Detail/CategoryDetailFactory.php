<?php

namespace Shopsys\FrameworkBundle\Model\Category\Detail;

use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
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
}
