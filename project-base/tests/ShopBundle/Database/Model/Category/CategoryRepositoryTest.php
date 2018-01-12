<?php

namespace Tests\ShopBundle\Database\Model\Category;

use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class CategoryRepositoryTest extends DatabaseTestCase
{
    const DOMAIN_ID = 1;

    public function testDoNotGetCategoriesWithoutVisibleChildren()
    {
        $categoryFacade = $this->getServiceByType(CategoryFacade::class);
        /* @var $categoryFacade \Shopsys\ShopBundle\Model\Category\CategoryFacade */
        $categoryRepository = $this->getServiceByType(CategoryRepository::class);
        /* @var $categoryRepository \Shopsys\ShopBundle\Model\Category\CategoryRepository */
        $categoryVisibilityRepository = $this->getServiceByType(CategoryVisibilityRepository::class);
        /* @var $categoryVisibilityRepository \Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository */

        $categoryData = new CategoryData();
        $categoryData->name = ['en' => 'name'];
        $categoryData->hiddenOnDomains = [];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->hiddenOnDomains = [self::DOMAIN_ID];
        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::DOMAIN_ID);
        $this->assertCount(0, $categoriesWithVisibleChildren);
    }

    public function testGetCategoriesWithAtLeastOneVisibleChild()
    {
        $categoryFacade = $this->getServiceByType(CategoryFacade::class);
        /* @var $categoryFacade \Shopsys\ShopBundle\Model\Category\CategoryFacade */
        $categoryRepository = $this->getServiceByType(CategoryRepository::class);
        /* @var $categoryRepository \Shopsys\ShopBundle\Model\Category\CategoryRepository */
        $categoryVisibilityRepository = $this->getServiceByType(CategoryVisibilityRepository::class);
        /* @var $categoryVisibilityRepository \Shopsys\ShopBundle\Model\Category\CategoryVisibilityRepository */

        $categoryData = new CategoryData();
        $categoryData->name = ['en' => 'name'];
        $categoryData->hiddenOnDomains = [];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->hiddenOnDomains = [];
        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::DOMAIN_ID);
        $this->assertCount(1, $categoriesWithVisibleChildren);
    }
}
