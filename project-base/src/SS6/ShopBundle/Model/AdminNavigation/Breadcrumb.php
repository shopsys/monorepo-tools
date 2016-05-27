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
	private $lastItem;

	public function __construct(Menu $menu) {
		$this->menu = $menu;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
	 */
	public function replaceLastItem(MenuItem $menuItem) {
		$this->lastItem = $menuItem;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	public function getItems($route, $routeParameters) {
		$items = $this->menu->getMenuPath($route, $routeParameters);

		if ($this->lastItem !== null) {
			array_pop($items);
			$items[] = $this->lastItem;
		}

		return $items;
	}

}
