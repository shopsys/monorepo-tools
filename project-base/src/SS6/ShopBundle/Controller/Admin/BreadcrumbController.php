<?php

namespace SS6\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BreadcrumbController extends Controller {

	public function indexAction() {
		$breadcrumb = $this->get('ss6.shop.admin_navigation.breadcrumb');
		/* @var $breadcrumb \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb */

		$items = $breadcrumb->getItems();

		return $this->render('@SS6Shop/Admin/Inline/Breadcrumb/breadcrumb.html.twig', array(
			'items' => $items,
		));
	}

}
