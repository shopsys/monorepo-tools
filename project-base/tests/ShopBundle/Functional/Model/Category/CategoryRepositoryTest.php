<?php

namespace Tests\ShopBundle\Functional\Model\Category;

use Shopsys\FrameworkBundle\Model\Category\CategoryDataFactoryInterface;
use Shopsys\FrameworkBundle\Model\Category\CategoryFacade;
use Shopsys\FrameworkBundle\Model\Category\CategoryRepository;
use Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository;
use Tests\ShopBundle\Test\TransactionFunctionalTestCase;

class CategoryRepositoryTest extends TransactionFunctionalTestCase
{
    protected const FIRST_DOMAIN_ID = 1;
    protected const SECOND_DOMAIN_ID = 2;

    public function testDoNotGetCategoriesWithoutVisibleChildren()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade */
        $categoryFacade = $this->getContainer()->get(CategoryFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository $categoryRepository */
        $categoryRepository = $this->getContainer()->get(CategoryRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository $categoryVisibilityRepository */
        $categoryVisibilityRepository = $this->getContainer()->get(CategoryVisibilityRepository::class);
        /** @var \Shopsys\ShopBundle\Model\Category\CategoryDataFactory $categoryDataFactory */
        $categoryDataFactory = $this->getContainer()->get(CategoryDataFactoryInterface::class);

        $categoryData = $categoryDataFactory->create();
        $categoryData->name = ['en' => 'name', 'cs' => 'name'];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->enabled[self::FIRST_DOMAIN_ID] = false;
        $categoryData->enabled[self::SECOND_DOMAIN_ID] = false;

        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::FIRST_DOMAIN_ID);
        $this->assertCount(0, $categoriesWithVisibleChildren);
    }

    public function testGetCategoriesWithAtLeastOneVisibleChild()
    {
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryFacade $categoryFacade */
        $categoryFacade = $this->getContainer()->get(CategoryFacade::class);
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository $categoryRepository */
        $categoryRepository = $this->getContainer()->get(CategoryRepository::class);
        /** @var \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRepository $categoryVisibilityRepository */
        $categoryVisibilityRepository = $this->getContainer()->get(CategoryVisibilityRepository::class);
        /** @var \Shopsys\ShopBundle\Model\Category\CategoryDataFactory $categoryDataFactory */
        $categoryDataFactory = $this->getContainer()->get(CategoryDataFactoryInterface::class);

        $categoryData = $categoryDataFactory->create();
        $categoryData->name = ['en' => 'name', 'cs' => 'name'];
        $categoryData->parent = $categoryFacade->getRootCategory();

        $parentCategory = $categoryFacade->create($categoryData);

        $categoryData->parent = $parentCategory;
        $categoryFacade->create($categoryData);

        $categoryVisibilityRepository->refreshCategoriesVisibility();

        $categoriesWithVisibleChildren = $categoryRepository->getCategoriesWithVisibleChildren([$parentCategory], self::FIRST_DOMAIN_ID);
        $this->assertCount(1, $categoriesWithVisibleChildren);
    }
}
