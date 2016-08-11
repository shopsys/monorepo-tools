<?php

namespace SS6\ShopBundle\Component\Category;

use SS6\ShopBundle\Model\Category\CategoryFacade;
use Symfony\Component\HttpFoundation\Request;

class CurrentCategoryResolver {

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	public function __construct(
		CategoryFacade $categoryFacade
	) {
		$this->categoryFacade = $categoryFacade;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return \SS6\ShopBundle\Model\Category\Category|null
	 */
	public function findCurrentCategoryByRequest(Request $request) {
		$routeName = $request->get('_route');

		if ($routeName === 'front_product_list') {
			$categoryId = $request->get('id');
			$currentCategory = $this->categoryFacade->getById($categoryId);

			return $currentCategory;
		}

		return null;
	}

}
