<?php

namespace SS6\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {

	public function menuAction($route, array $parameters = null) {
		$menu = $this->get('ss6.shop.admin_menu.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminMenu\Menu */

		$activePath = array();
		$matchingItem = $menu->getItemMatchingRoute($route, $parameters);
		if ($matchingItem !== null) {
			$activePath = $menu->getItemPath($matchingItem);
		}

		return $this->render('@SS6Shop/Admin/Inline/Menu/menu.html.twig', array(
			'menu' => $menu,
			'activePath' => $activePath,
		));
	}

	public function panelAction($route, array $parameters = null) {
		$menu = $this->get('ss6.shop.admin_menu.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminMenu\Menu */

		$items = array();
		$activePath = array();

		$matchingItem = $menu->getItemMatchingRoute($route, $parameters);
		if ($matchingItem !== null) {
			$activePath = $menu->getItemPath($matchingItem);
			if (isset($activePath[0])) {
				$items = $activePath[0]->getItems();
			}
		}

		return $this->render('@SS6Shop/Admin/Inline/Menu/panel.html.twig', array(
			'items' => $items,
			'activePath' => $activePath,
		));
	}

}
