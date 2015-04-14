<?php

namespace SS6\ShopBundle\Tests\Unit\Config;

use SS6\ShopBundle\Component\Test\FunctionalTestCase;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;

class AdminNavigationTest extends FunctionalTestCase {

	public function testHasSettingsItem() {
		$menu = $this->getContainer()->get('ss6.shop.admin_navigation.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminNavigation\Menu */

		$this->assertInstanceOf(MenuItem::class, $menu->getSettingsItem());
	}

	public function testResolveRoutes() {
		$menu = $this->getContainer()->get('ss6.shop.admin_navigation.menu');
		/* @var $menu \SS6\ShopBundle\Model\AdminNavigation\Menu */

		$this->resolveRoutesRecursive($menu->getItems());
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem[] $items
	 */
	private function resolveRoutesRecursive(array $items) {
		$router = $this->getContainer()->get('router');
		/* @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */

		foreach ($items as $item) {
			if ($item->isVisible()) {
				if ($item->getRoute() !== null) {
					$router->generate($item->getRoute(), $item->getRouteParameters());
				}

				if ($item->getItems() !== null) {
					$this->resolveRoutesRecursive($item->getItems());
				}
			}
		}
	}

}
