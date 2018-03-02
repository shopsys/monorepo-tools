<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

class Breadcrumb
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuFactory
     */
    private $menuFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem|null
     */
    private $overridingLastItem;

    public function __construct(MenuFactory $menuFactory)
    {
        $this->menuFactory = $menuFactory;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem $menuItem
     */
    public function overrideLastItem(MenuItem $menuItem)
    {
        $this->overridingLastItem = $menuItem;
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param array|null $routeParameters
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getItems($route, $routeParameters)
    {
        $menu = $this->menuFactory->createMenuWithVisibleItems();
        $items = $menu->getMenuPath($route, $routeParameters);

        if ($this->overridingLastItem !== null) {
            array_pop($items);
            $items[] = $this->overridingLastItem;
        }

        return $items;
    }
}
