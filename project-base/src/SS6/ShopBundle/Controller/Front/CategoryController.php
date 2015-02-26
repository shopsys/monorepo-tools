<?php

namespace SS6\ShopBundle\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CategoryController extends Controller {

	public function panelAction() {
		$domain = $this->get('ss6.shop.domain');
		/* @var $domain \SS6\ShopBundle\Model\Domain\Domain */
		$categoryFacade = $this->get('ss6.shop.category.category_facade');
		/* @var $categoryFacade \SS6\ShopBundle\Model\Category\CategoryFacade */

		$categories = $categoryFacade->getAllInRootWithTranslation($domain->getLocale());

		return $this->render('@SS6Shop/Front/Content/Category/panel.html.twig', [
			'categories' => $categories,
		]);
	}

}
