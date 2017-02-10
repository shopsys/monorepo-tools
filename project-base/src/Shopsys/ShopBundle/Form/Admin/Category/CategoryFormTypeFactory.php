<?php

namespace Shopsys\ShopBundle\Form\Admin\Category;

use Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType;
use Shopsys\ShopBundle\Model\Category\Category;
use Shopsys\ShopBundle\Model\Category\CategoryRepository;
use Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository;

class CategoryFormTypeFactory {

	/**
	 * @var \Shopsys\ShopBundle\Model\Category\CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * @var \Shopsys\ShopBundle\Model\Feed\Category\FeedCategoryRepository
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
	 * @return \Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType
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
	 * @param \Shopsys\ShopBundle\Model\Category\Category $category
	 * @return \Shopsys\ShopBundle\Form\Admin\Category\CategoryFormType
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
