<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller {

	public function panelAction() {
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$categories = $categoryFacade->getAllInRootWithTranslation();

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'categories' => $categories,
		]);
	}

}
