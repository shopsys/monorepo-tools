<?php

namespace SS6\ShopBundle\Model\Category\Detail;

use SS6\ShopBundle\Model\Category\Category;

class CategoryDetailFactory {

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	public function createDetailsHierarchy(array $categories) {
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
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @param \SS6\ShopBundle\Model\Category\Category[parentId] $categoriesByParentId
	 * @return \SS6\ShopBundle\Model\Category\Detail\CategoryDetail[]
	 */
	private function getChildrenDetails(Category $category, array $categoriesByParentId) {
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
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @return \SS6\ShopBundle\Model\Category\Category[]
	 */
	private function getFirstLevelCategories(array $categories) {
		$firstLevelCategories = [];

		foreach ($categories as $category) {
			if ($category->getLevel() === 1) {
				$firstLevelCategories[] = $category;
			}
		}

		return $firstLevelCategories;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category[] $categories
	 * @return \SS6\ShopBundle\Model\Category\Category[parentId][]
	 */
	private function getCategoriesIndexedByParentId(array $categories) {
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
