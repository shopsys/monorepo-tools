<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SearchController extends Controller {

	public function autocompleteAction() {
		$result = [
			'label' => 'Celkem nalezeno 31 produktů',
			'products' => [
				[
					'name' => 'Product name 1',
					'url' => '#product1',
					'imageUrl' => '/assets/content/images/noimage.gif'
				],
				[
					'name' => 'Product name 2',
					'url' => '#product2',
					'imageUrl' => '/assets/content/images/noimage.gif'
				],
			],
		];
		
		return new JsonResponse($result);
	}

}
