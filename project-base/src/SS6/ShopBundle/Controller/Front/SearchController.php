<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

	const AUTOCOMPLETE_CATEGORY_LIMIT = 3;
	const AUTOCOMPLETE_PRODUCT_LIMIT = 5;

	/**
	 * @var \SS6\ShopBundle\Model\Category\CategoryFacade
	 */
	private $categoryFacade;

	/**
	 * @var \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade
	 */
	private $productOnCurrentDomainFacade;

	public function __construct(
		CategoryFacade $categoryFacade,
		ProductOnCurrentDomainFacade $productOnCurrentDomainFacade
	) {
		$this->categoryFacade = $categoryFacade;
		$this->productOnCurrentDomainFacade = $productOnCurrentDomainFacade;
	}

	public function autocompleteAction(Request $request) {
		$searchText = $request->get('searchText');

		$categoriesPaginationResult = $this->categoryFacade
			->getSearchAutocompleteCategories($searchText, self::AUTOCOMPLETE_CATEGORY_LIMIT);

		$productsPaginationResult = $this->productOnCurrentDomainFacade
			->getSearchAutocompleteProducts($searchText, self::AUTOCOMPLETE_PRODUCT_LIMIT);

		return $this->render('@SS6Shop/Front/Content/Search/autocomplete.html.twig', [
			'searchText' => $searchText,
			'categoriesPaginationResult' => $categoriesPaginationResult,
			'productsPaginationResult' => $productsPaginationResult,
		]);
	}

	public function boxAction(Request $request) {
		$searchText = $request->query->get(ProductController::SEARCH_TEXT_PARAMETER);

		return $this->render('@SS6Shop/Front/Content/Search/searchBox.html.twig', [
			'searchText' => $searchText,
		]);
	}

}
