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

	public function panelAction($route, array $parameters = null) {
		$menu = $this->get('ss6.shop.admin_menu.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminMenu\Menu */

		$items = array();

		$activeItem = $menu->getItemMatchingRoute($route, $parameters);
		if ($activeItem !== null) {
			$path = $menu->getItemPath($activeItem);
				if (isset($path[0])) {
				$items = $path[0]->getItems();
			}
		}

		return $this->render('@SS6Shop/Admin/Inline/Menu/panel.html.twig', array(
			'items' => $items,
		));
	}

}
