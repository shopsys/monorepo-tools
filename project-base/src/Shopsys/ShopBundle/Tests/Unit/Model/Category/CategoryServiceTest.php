<?php

namespace Shopsys\ShopBundle\Tests\Unit\Model\Category;

use PHPUnit_Framework_TestCase;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryData;
use Shopsys\ShopBundle\Model\Category\CategoryService;

class CategoryServiceTest extends PHPUnit_Framework_TestCase {

    public function testCreateSetRoot() {
        $categoryData = new CategoryData();
        $rootCategory = new Category($categoryData);

        $categoryService = new CategoryService();
        $category = $categoryService->create($categoryData, $rootCategory);

        $this->assertSame($rootCategory, $category->getParent());
    }

    public function testCreate() {
        $rootCategory = new Category(new CategoryData());
        $parentCategory = new Category(new CategoryData());
        $categoryData = new CategoryData();
        $categoryData->parent = $parentCategory;

        $categoryService = new CategoryService();
        $category = $categoryService->create($categoryData, $rootCategory);

        $this->assertSame($parentCategory, $category->getParent());
    }

    public function testEditSetRoot() {
        $categoryData = new CategoryData();
        $rootCategory = new Category($categoryData);
        $category = new Category(new CategoryData());

        $categoryService = new CategoryService();
        $categoryService->edit($category, $categoryData, $rootCategory);

        $this->assertSame($rootCategory, $category->getParent());
    }

    public function testEdit() {
        $rootCategory = new Category(new CategoryData());
        $parentCategory = new Category(new CategoryData());
        $categoryData = new CategoryData();
        $categoryData->parent = $parentCategory;
        $category = new Category(new CategoryData());

        $categoryService = new CategoryService();
        $categoryService->edit($category, $categoryData, $rootCategory);

        $this->assertSame($parentCategory, $category->getParent());
    }

}
