<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

class Breadcrumb {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	private $menuItems;

	public function __construct() {
		$this->menuItems = [];
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
	 */
	public function addItem(MenuItem $menuItem) {
		$this->menuItems[] = $menuItem;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
	 */
	public function replaceLastItem(MenuItem $menuItem) {
		if (count($this->menuItems) > 0) {
			array_pop($this->menuItems);
		}
		$this->menuItems[] = $menuItem;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	public function getItems() {
		return $this->menuItems;
	}

}
