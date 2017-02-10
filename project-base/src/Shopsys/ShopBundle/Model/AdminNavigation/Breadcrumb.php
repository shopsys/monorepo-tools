<?php

namespace Shopsys\ShopBundle\Model\AdminNavigation;

use Shopsys\ShopBundle\Model\AdminNavigation\Menu;

class Breadcrumb
{
    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\MenuFactory
     */
    private $menuFactory;

    /**
     * @var \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem|null
     */
    private $overrdingLastItem;

    public function __construct(MenuFactory $menuFactory)
    {
        $this->menuFactory = $menuFactory;
    }

    /**
     * @param \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem $menuItem
     */
    public function overrideLastItem(MenuItem $menuItem)
    {
        $this->overrdingLastItem = $menuItem;
    }

    /**
     * @return \Shopsys\ShopBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getItems($route, $routeParameters)
    {
        $menu = $this->menuFactory->createMenuWithVisibleItems();
        $items = $menu->getMenuPath($route, $routeParameters);

        if ($this->overrdingLastItem !== null) {
            array_pop($items);
            $items[] = $this->overrdingLastItem;
        }

        return $items;
    }
}
