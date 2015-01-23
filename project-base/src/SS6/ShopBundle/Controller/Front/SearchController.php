<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller {

	const AUTOCOMPLETE_PRODUCT_LIMIT = 5;

	public function autocompleteAction(Request $request) {
		$translator = $this->get('translator');
		/* @var $translator \Symfony\Component\Translation\TranslatorInterface */
		$productOnCurrentDomainFacade = $this->get('ss6.shop.product.product_on_current_domain_facade');
		/* @var $productOnCurrentDomainFacade \SS6\ShopBundle\Model\Product\ProductOnCurrentDomainFacade */

		$searchText = $request->get('searchText');
		$result = $productOnCurrentDomainFacade->getSearchAutocompleteData($searchText, self::AUTOCOMPLETE_PRODUCT_LIMIT);

		$result['label'] = $translator->transChoice(
			'{0} Nebyl nalezen žádný produkt'
			. '|{1} Celkem nalezen 1 produkt'
			. '|[2,4] Celkem nalezeny %totalProductCount% produkty'
			. '|[5,Inf] Celkem nalezeno %totalProductCount% produktů',
			$result['totalProductCount'],
			[
				'%totalProductCount%' => $result['totalProductCount']
			]
		);
				
		return new JsonResponse($result);
	}

}
