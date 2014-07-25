<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

use \Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbSingletonFactory {

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	private $breadcrumb;

	/**
	 * @var \Symfony\Component\HttpFoundation\Request
	 */
	private $request;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	private $menu;

	/**
	 * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
	 * @param \SS6\ShopBundle\Model\AdminNavigation\Menu $menu
	 */
	public function __construct(RequestStack $requestStack, Menu $menu) {
		$this->request = $requestStack->getMasterRequest();
		$this->menu = $menu;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\Breadcrumb
	 */
	public function get() {
		if ($this->breadcrumb === null) {
			$this->breadcrumb = new Breadcrumb();
			$menuItems = $this->menu->getMenuPath($this->request->get('_route'), $this->request->get('_route_params'));
			foreach ($menuItems as $menuItem) {
				$this->breadcrumb->addItem($menuItem);
			}
		}

		return $this->breadcrumb;
	}

}
