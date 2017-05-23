<?php

namespace Shopsys\HttpSmokeTesting;

use Shopsys\HttpSmokeTesting\Exception\RouteNameNotFoundException;

class RouteConfigCustomizer
{
    /**
     * @var \Shopsys\HttpSmokeTesting\RequestDataSetGenerator[]
     */
    private $requestDataSetGenerators;

    /**
     * @param \Shopsys\HttpSmokeTesting\RequestDataSetGenerator[] $requestDataSetGenerators
     */
    public function __construct(array $requestDataSetGenerators)
    {
        $this->requestDataSetGenerators = $requestDataSetGenerators;
    }

    /**
     * Provided $callback will be called with RouteConfig and RouteInfo as arguments
     *
     * @see \Shopsys\HttpSmokeTesting\RouteConfig
     * @see \Shopsys\HttpSmokeTesting\RouteInfo
     * @param callable $callback
     * @return \Shopsys\HttpSmokeTesting\RouteConfigCustomizer
     */
    public function customize($callback)
    {
        foreach ($this->requestDataSetGenerators as $requestDataSetGenerator) {
            $callback($requestDataSetGenerator, $requestDataSetGenerator->getRouteInfo());
        }

        return $this;
    }

    /**
     * Provided $callback will be called with RouteConfig and RouteInfo that matches by route name as arguments
     *
     * @see \Shopsys\HttpSmokeTesting\RouteConfig
     * @see \Shopsys\HttpSmokeTesting\RouteInfo
     * @param string|string[] $routeName
     * @param callable $callback
     * @return \Shopsys\HttpSmokeTesting\RouteConfigCustomizer
     */
    public function customizeByRouteName($routeName, $callback)
    {
        $routeNames = (array)$routeName;
        $foundRouteNames = [];
        foreach ($this->requestDataSetGenerators as $requestDataSetGenerator) {
            $routeInfo = $requestDataSetGenerator->getRouteInfo();
            if (in_array($routeInfo->getRouteName(), $routeNames, true)) {
                $callback($requestDataSetGenerator, $routeInfo);
                $foundRouteNames[] = $routeInfo->getRouteName();
            }
        }

        $notFoundRouteNames = array_diff($routeNames, $foundRouteNames);
        if (count($notFoundRouteNames) > 0) {
            throw new RouteNameNotFoundException($notFoundRouteNames);
        }

        return $this;
    }
}
