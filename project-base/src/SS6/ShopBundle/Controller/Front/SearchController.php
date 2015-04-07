<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

	const AUTOCOMPLETE_PRODUCT_LIMIT = 5;

	public function autocompleteAction(Request $request) {
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */

		$searchText = $request->get('searchText');

		$productsPaginationResult = $productOnCurrentDomainFacade
			->getSearchAutocompleteProducts($searchText, self::AUTOCOMPLETE_PRODUCT_LIMIT);

		return $this->render('@SS6Shop/Front/Content/Search/autocomplete.html.twig', [
			'searchText' => $searchText,
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
