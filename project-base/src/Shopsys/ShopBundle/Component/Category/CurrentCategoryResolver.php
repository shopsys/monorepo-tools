<?php

namespace Shopsys\ShopBundle\Component\Category;

use Shopsys\ShopBundle\Model\Category\CategoryFacade;
use Shopsys\ShopBundle\Model\Product\ProductEditFacade;
use Symfony\Component\HttpFoundation\Request;

class CurrentCategoryResolver {

	/**
	 * @var \Shopsys\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \Shopsys\ShopBundle\Model\Product\ProductEditFacade
	 */
	private $productEditFacade;

	public function __construct(
		CategoryFacade $categoryFacade,
		ProductEditFacade $productEditFacade
	) {
		$this->categoryFacade = $categoryFacade;
		$this->productEditFacade = $productEditFacade;
	}

	/**
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param int $domainId
	 * @return \Shopsys\ShopBundle\Model\Category\Category|null
	 */
	public function findCurrentCategoryByRequest(Request $request, $domainId) {
		$routeName = $request->get('_route');

		if ($routeName === 'front_product_list') {
			$categoryId = $request->get('id');
			$currentCategory = $this->categoryFacade->getById($categoryId);

			return $currentCategory;
		} elseif ($routeName === 'front_product_detail') {
			$productId = $request->get('id');
			$product = $this->productEditFacade->getById($productId);
			$currentCategory = $this->categoryFacade->getProductMainCategoryByDomainId($product, $domainId);

			return $currentCategory;
		}

		return null;
	}

}
