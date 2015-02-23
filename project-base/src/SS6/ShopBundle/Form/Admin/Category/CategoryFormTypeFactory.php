<?php

namespace SS6\ShopBundle\Form\Admin\Category;

use SS6\ShopBundle\Form\Admin\Category\CategoryFormType;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryRepository;

class CategoryFormTypeFactory {

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
	 * @return \SS6\ShopBundle\Form\Admin\Category\CategoryFormType
	 */
	public function create() {
		$categories = $this->categoryRepository->getAll();

		return new CategoryFormType(
			$categories
		);
	}

	/**
	 * @param \SS6\ShopBundle\Form\Admin\Product\Category $category
	 * @return \SS6\ShopBundle\Form\Admin\Category\CategoryFormType
	 */
	public function createForCategory(Category $category) {
		$categories = $this->categoryRepository->getAllWithoutBranch($category);

		return new CategoryFormType(
			$categories
		);
	}

}
