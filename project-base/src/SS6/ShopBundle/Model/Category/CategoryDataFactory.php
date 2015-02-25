<?php

namespace SS6\ShopBundle\Model\Category;

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

	public function createFromCategory(Category $category) {
		$categoryDomains = $this->categoryRepository->getCategoryDomainsByCategory($category);

		$categoryData = new CategoryData();
		$categoryData->setFromEntity($category, $categoryDomains);

		return $categoryData;
	}

	public function createDefault() {
		$categoryData = new CategoryData();

		return $categoryData;
	}

}
