<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;

class CategoryService {

	/**
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @param \SS6\ShopBundle\Model\Category\Category $rootCategory
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function create(CategoryData $categoryData, Category $rootCategory) {
		$category = new Category($categoryData);
		if ($category->getParent() === null) {
			$category->setParent($rootCategory);
		}

		return $category;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Category\CategoryData $categoryData
	 * @param \SS6\ShopBundle\Model\Category\Category $rootCategory
	 * @return \SS6\ShopBundle\Model\Category\Category
	 */
	public function edit(Category $category, CategoryData $categoryData, Category $rootCategory) {
		$category->edit($categoryData);
		if ($category->getParent() === null) {
			$category->setParent($rootCategory);
		}

		return $category;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 */
	public function setChildrenAsSiblings(Category $category) {
		foreach ($category->getChildren() as $child) {
			$child->setParent($category->getParent());
		}
	}

}
