<?php

namespace SS6\ShopBundle\Model\Category;

use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryData;

class CategoryDataFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	public function __construct(
		CategoryRepository $categoryRepository
	) {
		$this->categoryRepository = $categoryRepository;
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Model\Category\CategoryData
	 */
	public function createFromCategory(Category $category) {
		$categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);

		$categoryData = new CategoryData();
		$categoryData->setFromEntity($category, $categoryDomains);

		return $categoryData;
	}

	/**
	 * @return \SS6\ShopBundle\Model\Category\CategoryData
	 */
	public function createDefault() {
		$categoryData = new CategoryData();

		return $categoryData;
	}

}
