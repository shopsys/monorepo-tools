<?php

namespace SS6\ShopBundle\Form\Admin\Category;

use SS6\ShopBundle\Form\Admin\Category\CategoryFormType;
use SS6\ShopBundle\Model\Category\Category;
use SS6\ShopBundle\Model\Category\CategoryRepository;
use SS6\ShopBundle\Model\Feed\Category\FeedCategoryRepository;

class CategoryFormTypeFactory {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \SS6\ShopBundle\Model\Feed\Category\FeedCategoryRepository
	 */
	private $feedCategoryRepository;

	public function __construct(
		CategoryRepository $categoryRepository,
		FeedCategoryRepository $feedCategoryRepository
	) {
		$this->categoryRepository = $categoryRepository;
		$this->feedCategoryRepository = $feedCategoryRepository;
	}

	/**
	 * @return \SS6\ShopBundle\Form\Admin\Category\CategoryFormType
	 */
	public function create() {
		$categories = $this->categoryRepository->getAll();
		$heurekaCzFeedCategories = $this->feedCategoryRepository->getAllHeurekaCz();

		return new CategoryFormType(
			$categories,
			$heurekaCzFeedCategories
		);
	}

	/**
	 * @param \SS6\ShopBundle\Model\Category\Category $category
	 * @return \SS6\ShopBundle\Form\Admin\Category\CategoryFormType
	 */
	public function createForCategory(Category $category) {
		$categories = $this->categoryRepository->getAllWithoutBranch($category);
		$heurekaCzFeedCategories = $this->feedCategoryRepository->getAllHeurekaCz();

		return new CategoryFormType(
			$categories,
			$heurekaCzFeedCategories,
			$category
		);
	}

}
