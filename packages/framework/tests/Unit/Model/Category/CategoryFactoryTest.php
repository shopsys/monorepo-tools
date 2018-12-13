<?php

namespace Tests\FrameworkBundle\Unit\Model\Category;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Category\Category;
use Shopsys\FrameworkBundle\Model\Category\CategoryData;
use Shopsys\FrameworkBundle\Model\Category\CategoryFactory;

class CategoryFactoryTest extends TestCase
{
    public function testCreateSetRoot()
    {
        $categoryData = new CategoryData();
        $rootCategory = new Category($categoryData);

        $categoryFactory = new CategoryFactory(new EntityNameResolver([]));
        $category = $categoryFactory->create($categoryData, $rootCategory);

        $this->assertSame($rootCategory, $category->getParent());
    }

    public function testCreate()
    {
        $rootCategory = new Category(new CategoryData());
        $parentCategory = new Category(new CategoryData());
        $categoryData = new CategoryData();
        $categoryData->parent = $parentCategory;

        $categoryFactory = new CategoryFactory(new EntityNameResolver([]));
        $category = $categoryFactory->create($categoryData, $rootCategory);

        $this->assertSame($parentCategory, $category->getParent());
    }
}
