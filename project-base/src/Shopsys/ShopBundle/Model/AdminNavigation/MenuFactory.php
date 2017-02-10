<?php

namespace Shopsys\ShopBundle\Model\AdminNavigation;

use Shopsys\ShopBundle\Component\Domain\Domain;
use Shopsys\ShopBundle\Model\Security\Roles;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuFactory
{
    /**
     * @var string
     */
    private $configFilepath;

    /**
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\MenuLoader
     */
    private $menuLoader;

    /**
     * @var \Shopsys\ShopBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param string $configFilepath
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
     * @param \Shopsys\ShopBundle\Model\AdminNavigation\MenuLoader $menuLoader
     * @param \Shopsys\ShopBundle\Component\Domain\Domain $domain
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
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\Menu
     */
    public function createMenuWithVisibleItems() {
        $menu = $this->menuLoader->loadFromYaml($this->configFilepath);

        $visibleMenuItems = $this->filterVisibleRecursive($menu->getItems());

        return new Menu($visibleMenuItems);
    }

    /**
     * @param \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem[] $menuItems
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem[]
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
     * @param \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
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
