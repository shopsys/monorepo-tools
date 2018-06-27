<?php

namespace Shopsys\FrameworkBundle\Model\AdminNavigation;

class MenuItem
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    private $items;

    /**
     * @var string|null
     */
    private $route;

    /**
     * @var array|null
     */
    private $routeParameters;

    /**
     * @var bool
     */
    private $visible;

    /**
     * @var bool
     */
    private $superadmin;

    /**
     * @var string|null
     */
    private $icon;

    /**
     * @var bool
     */
    private $multidomainOnly;

    /**
     * @param string $label
     * @param string|null $route
     * @param array|null $routeParameters
     * @param bool $visible
     * @param bool $superadmin
     * @param string|null $icon
     * @param bool $multidomainOnly
     * @param array $items
     */
    public function __construct(
        $label,
        $route = null,
        array $routeParameters = null,
        $visible = true,
        $superadmin = false,
        $icon = null,
        $multidomainOnly = false,
        array $items = []
    ) {
        $this->label = $label;
        $this->route = $route;

        if (isset($routeParameters)) {
            $this->routeParameters = $routeParameters;
        } else {
            $this->routeParameters = [];
        }

        if (isset($visible)) {
            $this->visible = $visible;
        } else {
            $this->visible = true;
        }

        if (isset($superadmin)) {
            $this->superadmin = $superadmin;
        } else {
            $this->superadmin = false;
        }

        if (isset($icon)) {
            $this->icon = $icon;
        } else {
            $this->icon = null;
        }

        $this->items = $items;
        $this->multidomainOnly = $multidomainOnly;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\AdminNavigation\MenuItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string|null
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array|null
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible === true;
    }

    /**
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->superadmin === true;
    }

    /**
     * @return bool
     */
    public function isMultidomainOnly()
    {
        return $this->multidomainOnly;
    }

    /**
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }
}
