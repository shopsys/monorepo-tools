<?php

namespace SS6\ShopBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {

	public function menuAction($route, array $parameters = null) {
		$menu = $this->get('ss6.shop.admin_navigation.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminNavigation\Menu */

		$activePath = $menu->getMenuPath($route, $parameters);

		return $this->render('@SS6Shop/Admin/Inline/Menu/menu.html.twig', [
			'menu' => $menu,
			'activePath' => $activePath,
		]);
	}

	public function panelAction($route, array $parameters = null) {
		$menu = $this->get('ss6.shop.admin_navigation.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminNavigation\Menu */

		$activePath = $menu->getMenuPath($route, $parameters);

		$secondLevelItems = [];
		if (isset($activePath[0])) {
			$secondLevelItems = $activePath[0]->getItems();
		}

		return $this->render('@SS6Shop/Admin/Inline/Menu/panel.html.twig', [
			'items' => $secondLevelItems,
			'activePath' => $activePath,
		]);
	}
}
