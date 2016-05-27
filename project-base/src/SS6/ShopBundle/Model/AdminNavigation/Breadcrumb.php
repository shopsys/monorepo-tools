<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

use SS6\ShopBundle\Model\AdminNavigation\Menu;

class Breadcrumb {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	private $menu;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\MenuItem|null
	 */
	private $overrdingLastItem;

	public function __construct(Menu $menu) {
		$this->menu = $menu;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
	 */
	public function overrideLastItem(MenuItem $menuItem) {
		$this->overrdingLastItem = $menuItem;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	public function getItems($route, $routeParameters) {
		$items = $this->menu->getMenuPath($route, $routeParameters);

		if ($this->overrdingLastItem !== null) {
			array_pop($items);
			$items[] = $this->overrdingLastItem;
		}

		return $items;
	}

}
