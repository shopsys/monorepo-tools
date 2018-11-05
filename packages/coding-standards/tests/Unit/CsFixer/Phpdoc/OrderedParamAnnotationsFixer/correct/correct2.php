<?php

class SomeClass
{
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
    }
}
