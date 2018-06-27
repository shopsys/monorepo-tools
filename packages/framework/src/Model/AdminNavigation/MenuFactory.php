<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Security\Roles;
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
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuLoader
     */
    private $menuLoader;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    private $domain;

    /**
     * @param string $configFilepath
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $authorizationChecker
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuLoader $menuLoader
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
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
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\Menu
     */
    public function createMenuWithAllowedItems()
    {
        $menu = $this->menuLoader->loadFromYaml($this->configFilepath);

        $allowedMenuItems = $this->filterAllowedRecursive($menu->getItems());

        return new Menu($allowedMenuItems);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[] $menuItems
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    private function filterAllowedRecursive(array $menuItems)
    {
        $allowedMenuItems = [];

        foreach ($menuItems as $menuItem) {
            if ($this->isMenuItemAllowed($menuItem)) {
                $allowedSubitems = $this->filterAllowedRecursive($menuItem->getItems());

                $allowedMenuItems[] = new MenuItem(
                    $menuItem->getLabel(),
                    $menuItem->getType(),
                    $menuItem->getRoute(),
                    $menuItem->getRouteParameters(),
                    $menuItem->isVisible(),
                    $menuItem->isSuperadmin(),
                    $menuItem->getIcon(),
                    $menuItem->isMultidomainOnly(),
                    $allowedSubitems
                );
            }
        }

        return $allowedMenuItems;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem $menuItem
     * @return bool
     */
    private function isMenuItemAllowed(MenuItem $menuItem)
    {
        if ($menuItem->isSuperadmin() && !$this->authorizationChecker->isGranted(Roles::ROLE_SUPER_ADMIN)) {
            return false;
        }

        if ($menuItem->isMultidomainOnly() && !$this->domain->isMultidomain()) {
            return false;
        }

        return true;
    }
}
