<?php

namespace SS6\ShopBundle\Controller\Front;

use SS6\ShopBundle\Model\Category\CategoryFacade;
use SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

	const AUTOCOMPLETE_CATEGORY_LIMIT = 3;
	const AUTOCOMPLETE_PRODUCT_LIMIT = 5;

	public function autocompleteAction(Request $request) {
		$productOnCurrentDomainFacade = $this->get(ProductOnCurrentDomainFacade::class);
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */
		$categoryFacade = $this->get(CategoryFacade::class);
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$searchText = $request->get('searchText');

		$categoriesPaginationResult = $categoryFacade
			->getSearchAutocompleteCategories($searchText, self::AUTOCOMPLETE_CATEGORY_LIMIT);

		$productsPaginationResult = $productOnCurrentDomainFacade
			->getSearchAutocompleteProducts($searchText, self::AUTOCOMPLETE_PRODUCT_LIMIT);

		return $this->render('@SS6Shop/Front/Content/Search/autocomplete.html.twig', [
			'searchText' => $searchText,
			'categoriesPaginationResult' => $categoriesPaginationResult,
			'productsPaginationResult' => $productsPaginationResult,
		]);
	}

	public function boxAction(Request $request) {
		$searchText = $request->query->get('q');

		return $this->render('@SS6Shop/Front/Content/Search/searchBox.html.twig', [
			'searchText' => $searchText,
		]);
	}

}
