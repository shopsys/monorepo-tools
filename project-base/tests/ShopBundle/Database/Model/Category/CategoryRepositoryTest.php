<?php

namespace Tests\ShopBundle\Database\Model\Category;

use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;
use Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository;
use Tests\ShopBundle\Test\DatabaseTestCase;

class CategoryRepositoryTest extends DatabaseTestCase
{
    const FIRST_DOMAIN_ID = 1;
    const SECOND_DOMAIN_ID = 2;

    public function testDoNotGetCategoriesWithoutVisibleChildren()
    {
        $categoryFacade = $this->getContainer()->get(CategoryFacade::class);
        /* @var $categoryFacade \Shopsys\FrameworkBundle\Model\Category\CategoryFacade */
        $categoryRepository = $this->getContainer()->get(CategoryRepository::class);
        /* @var $categoryRepository \Shopsys\FrameworkBundle\Model\Category\CategoryRepository */
        $categoryVisibilityRepository = $this->getContainer()->get(CategoryVisibilityRepository::class);
        /* @var $categoryVisibilityRepository \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository */
        $categoryDataFactory = $this->getContainer()->get(CategoryDataFactory::class);
        /* @var $categoryDataFactory \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory */

        $categoryData = $categoryDataFactory->createDefault();
        $categoryData->name = ['en' => 'name'];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->enabled = [
            self::FIRST_DOMAIN_ID => false,
            self::SECOND_DOMAIN_ID => false,
        ];
        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::FIRST_DOMAIN_ID);
        $this->assertCount(0, $categoriesWithVisibleChildren);
    }

    public function testGetCategoriesWithAtLeastOneVisibleChild()
    {
        $categoryFacade = $this->getContainer()->get(CategoryFacade::class);
        /* @var $categoryFacade \Shopsys\FrameworkBundle\Model\Category\CategoryFacade */
        $categoryRepository = $this->getContainer()->get(CategoryRepository::class);
        /* @var $categoryRepository \Shopsys\FrameworkBundle\Model\Category\CategoryRepository */
        $categoryVisibilityRepository = $this->getContainer()->get(CategoryVisibilityRepository::class);
        /* @var $categoryVisibilityRepository \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository */
        $categoryDataFactory = $this->getContainer()->get(CategoryDataFactory::class);
        /* @var $categoryDataFactory \Shopsys\FrameworkBundle\Model\Category\CategoryDataFactory */

        $categoryData = $categoryDataFactory->createDefault();
        $categoryData->name = ['en' => 'name'];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::FIRST_DOMAIN_ID);
        $this->assertCount(1, $categoriesWithVisibleChildren);
    }
}
