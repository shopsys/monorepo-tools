<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

class Menu
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    private $items;

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getVisibleItems()
    {
        return array_filter($this->items, function (MenuItem $item) {
            return $item->isVisible();
        });
    }

    /**
     * Finds deepest item matching specified route.
     *
     * @param string $route
     * @param array|null $parameters
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem|null
     */
    private function getItemMatchingRoute($route, array $parameters = null)
    {
        $item = $this->getItemMatchingRouteRecursive($this->getItems(), $route, $parameters);

        return $item;
    }

    /**
     * Finds deepest item matching specified route.
     *
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[] $items
     * @param string $route
     * @param array|null $parameters
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem|null
     */
    private function getItemMatchingRouteRecursive(array $items, $route, array $parameters = null)
    {
        foreach ($items as $item) {
            if ($item->getItems() !== null) {
                $matchingItem = $this->getItemMatchingRouteRecursive($item->getItems(), $route, $parameters);

                if ($matchingItem !== null) {
                    return $matchingItem;
                }
            }

            if ($this->isItemMatchingRoute($item, $route, $parameters)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem $item
     * @param string $route
     * @param array|null $parameters
     * @return bool
     */
    private function isItemMatchingRoute(MenuItem $item, $route, array $parameters = null)
    {
        if ($item->getRoute() !== $route) {
            return false;
        }

        if ($item->getRouteParameters() !== null) {
            foreach ($item->getRouteParameters() as $itemRouteParameterName => $itemRouteParameterValue) {
                if (!isset($parameters[$itemRouteParameterName])) {
                    return false;
                }

                if ($parameters[$itemRouteParameterName] != $itemRouteParameterValue) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem $item
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]|null
     */
    private function getItemPath(MenuItem $item)
    {
        return $this->getItemPathRecursive($this->getItems(), $item);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[] $items
     * @param \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem $item
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]|null
     */
    private function getItemPathRecursive(array $items, MenuItem $item)
    {
        foreach ($items as $subitem) {
            if ($subitem === $item) {
                return [$item];
            }

            if ($subitem->getItems() !== null) {
                $path = $this->getItemPathRecursive($subitem->getItems(), $item);

                if ($path !== null) {
                    array_unshift($path, $subitem);
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * @param string $route
     * @param array|null $parameters
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getMenuPath($route, $parameters)
    {
        $matchingItem = $this->getItemMatchingRoute($route, $parameters);
        if ($matchingItem === null) {
            throw new \Shopsys\FrameworkBundle\Model\AdminNavigation\Exception\MenuItemNotMatchingRouteException($route, $parameters);
        }

        return $this->getItemPath($matchingItem);
    }
}
