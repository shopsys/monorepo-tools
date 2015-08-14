<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Controller\Admin\BaseController;
use SS6\ShopBundle\Model\AdminNavigation\Menu;

class MenuController extends BaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	private $menu;

	public function __construct(Menu $menu) {
		$this->menu = $menu;
	}

	public function menuAction($route, array $parameters = null) {
		$activePath = $this->menu->getMenuPath($route, $parameters);

		return $this->render('@SS6Shop/Admin/Inline/Menu/menu.html.twig', [
			'menu' => $this->menu,
			'activePath' => $activePath,
		]);
	}

	public function panelAction($route, array $parameters = null) {
		$activePath = $this->menu->getMenuPath($route, $parameters);

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
