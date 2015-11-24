<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\AdminNavigation\Menu;
use SS6\ShopBundle\Model\Security\Roles;

class MenuController extends AdminBaseController {

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
			'ROLE_SUPER_ADMIN' => Roles::ROLE_SUPER_ADMIN,
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
			'ROLE_SUPER_ADMIN' => Roles::ROLE_SUPER_ADMIN,
		]);
	}
}
