<?php

namespace SS6\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {

	public function menuAction() {
		$menu = $this->get('ss6.shop.admin_menu.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminMenu\Menu */

		return $this->render('@SS6Shop/Admin/Inline/Menu/menu.html.twig', array(
			'menu' => $menu,
		));
	}

}
