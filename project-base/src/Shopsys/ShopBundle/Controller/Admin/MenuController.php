<?php

namespace SS6\ShopBundle\Controller\Admin;

use SS6\ShopBundle\Component\Controller\AdminBaseController;
use SS6\ShopBundle\Model\AdminNavigation\Menu;
use SS6\ShopBundle\Model\AdminNavigation\MenuFactory;

class MenuController extends AdminBaseController {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\MenuFactory
	 */
	private $menuFactory;

	public function __construct(MenuFactory $menuFactory) {
		$this->menuFactory = $menuFactory;
	}

	public function menuAction($route, array $parameters = null) {
		$menu = $this->menuFactory->createMenuWithVisibleItems();
		$activePath = $menu->getMenuPath($route, $parameters);

		return $this->render('@SS6Shop/Admin/Inline/Menu/menu.html.twig', [
			'menu' => $menu,
			'activePath' => $activePath,
		]);
	}

	public function panelAction($route, array $parameters = null) {
		$menu = $this->menuFactory->createMenuWithVisibleItems();
		$activePath = $menu->getMenuPath($route, $parameters);

		if (isset($activePath[1]) && $menu->isRouteMatchingDescendantOfSettings($route, $parameters)) {
			$panelItems = $activePath[1]->getItems();
		} elseif (isset($activePath[0])) {
			$panelItems = $activePath[0]->getItems();
		} else {
			$panelItems = null;
		}

		return $this->render('@SS6Shop/Admin/Inline/Menu/panel.html.twig', [
			'items' => $panelItems,
			'activePath' => $activePath,
		]);
	}
}
