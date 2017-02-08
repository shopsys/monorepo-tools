<?php

namespace SS6\ShopBundle\Model\AdminNavigation;

use SS6\ShopBundle\Component\Domain\Domain;
use SS6\ShopBundle\Model\Security\Roles;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuFactory {

	/**
	 * @var string
	 */
	private $configFilepath;

	/**
	 * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
	 */
	private $authorizationChecker;

	/**
	 * @var \SS6\ShopBundle\Model\AdminNavigation\MenuLoader
	 */
	private $menuLoader;

	/**
	 * @var \SS6\ShopBundle\Component\Domain\Domain
	 */
	private $domain;

	/**
	 * @param string $configFilepath
	 * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuLoader $menuLoader
	 * @param \SS6\ShopBundle\Component\Domain\Domain $domain
	 */
	public function __construct(
		$configFilepath,
		AuthorizationCheckerInterface $authorizationChecker,
		MenuLoader $menuLoader,
		Domain $domain
	) {
		$this->configFilepath = $configFilepath;
		$this->authorizationChecker = $authorizationChecker;
		$this->menuLoader = $menuLoader;
		$this->domain = $domain;
	}

	/**
	 * @return \SS6\ShopBundle\Model\AdminNavigation\Menu
	 */
	public function createMenuWithVisibleItems() {
		$menu = $this->menuLoader->loadFromYaml($this->configFilepath);

		$visibleMenuItems = $this->filterVisibleRecursive($menu->getItems());

		return new Menu($visibleMenuItems);
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem[] $menuItems
	 * @return \SS6\ShopBundle\Model\AdminNavigation\MenuItem[]
	 */
	private function filterVisibleRecursive(array $menuItems) {
		$visibleMenuItems = [];

		foreach ($menuItems as $menuItem) {
			if ($this->isMenuItemVisible($menuItem)) {
				$visibleSubitems = $this->filterVisibleRecursive($menuItem->getItems());

				$visibleMenuItems[] = new MenuItem(
					$menuItem->getLabel(),
					$menuItem->getType(),
					$menuItem->getRoute(),
					$menuItem->getRouteParameters(),
					$menuItem->isVisible(),
					$menuItem->isSuperadmin(),
					$menuItem->getIcon(),
					$menuItem->isMultidomainOnly(),
					$visibleSubitems
				);
			}
		}

		return $visibleMenuItems;
	}

	/**
	 * @param \SS6\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
	 * @return bool
	 */
	private function isMenuItemVisible(MenuItem $menuItem) {
		if ($menuItem->isSuperadmin() && !$this->authorizationChecker->isGranted(Roles::ROLE_SUPER_ADMIN)) {
			return false;
		}

		if ($menuItem->isMultidomainOnly() && !$this->domain->isMultidomain()) {
			return false;
		}

		return true;
	}

}
