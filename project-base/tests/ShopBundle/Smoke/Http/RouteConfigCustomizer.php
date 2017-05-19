<?php

namespace Tests\ShopBundle\Smoke\Http;

class RouteConfigCustomizer
{
    /**
     * @var \Tests\ShopBundle\Smoke\Http\RouteConfig[]
     */
    private $routeConfigs;

    public function __construct(array $routeConfigs)
    {
        $this->routeConfigs = $routeConfigs;
    }

    /**
     * Provided $callback will be called with RouteConfig as a single argument
     *
     * @see \Tests\ShopBundle\Smoke\Http\RouteConfig
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer
     */
    public function customize($callback)
    {
        array_map($callback, $this->routeConfigs);

        return $this;
    }

    /**
     * Provided $callback will be called with RouteConfig that matches by route name as a single argument
     *
     * @see \Tests\ShopBundle\Smoke\Http\RouteConfig
     * @param string|string[] $routeName
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfigCustomizer
     */
    public function customizeByRouteName($routeName, $callback)
    {
        $routeNames = (array)$routeName;
        $foundRouteNames = [];
        foreach ($this->routeConfigs as $config) {
            if (in_array($config->getRouteName(), $routeNames, true)) {
                $callback($config);
                $foundRouteNames[] = $config->getRouteName();
            }
        }

        $notFoundRouteNames = array_diff($routeNames, $foundRouteNames);
        if (count($notFoundRouteNames) > 0) {
            throw new RouteNameNotFoundException($notFoundRouteNames);
        }

        return $this;
    }
}
