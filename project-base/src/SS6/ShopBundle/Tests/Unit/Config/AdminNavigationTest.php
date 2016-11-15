<?php

namespace SS6\ShopBundle\Tests\Unit\Config;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\AdminNavigation\Menu;
use SS6\ShopBundle\Model\AdminNavigation\MenuFactory;
use SS6\ShopBundle\Model\AdminNavigation\MenuItem;
use SS6\ShopBundle\Model\AdminNavigation\MenuLoader;
use SS6\ShopBundle\Tests\Test\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AdminNavigationTest extends FunctionalTestCase {

	public function testHasSettingsItem() {
		$this->assertInstanceOf(MenuItem::class, $this->getMenu()->getSettingsItem());
	}

	public function testResolveRoutes() {
		$this->resolveRoutesRecursive($this->getMenu()->getItems());
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

				$this->resolveRoutesRecursive($item->getItems());
			}
		}
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	private function getMenu() {
		$authorizationChecker = $this->getMockBuilder(AuthorizationCheckerInterface::class)
			->setMethods(['isGranted'])
			->getMockForAbstractClass();
		$authorizationChecker->expects($this->any())
			->method('isGranted')
			->willReturn(true);

		$menuFactory = new MenuFactory(
			$this->getContainer()->getParameter('ss6.admin_navigation.config_filepath'),
			$authorizationChecker,
			$this->getContainer()->get(MenuLoader::class),
			$this->getContainer()->get(Domain::class)
		);

		return $menuFactory->createMenuWithVisibleItems();
	}

}
