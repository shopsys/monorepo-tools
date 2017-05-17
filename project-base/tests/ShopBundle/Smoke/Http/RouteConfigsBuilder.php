<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Component\Routing\RouterInterface;

class RouteConfigsBuilder
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    /**
     * @var \Tests\ShopBundle\Smoke\Http\RouteConfig[]|null
     */
    private $routeConfigs;

    /**
     * @param \Symfony\Component\Routing\RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig[]
     */
    private function getRouteConfigs()
    {
        if ($this->routeConfigs === null) {
            $this->routeConfigs = [];
            foreach ($this->router->getRouteCollection() as $routeName => $route) {
                $this->routeConfigs[] = new RouteConfig($routeName, $route);
            }
        }

        return $this->routeConfigs;
    }

    /**
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfigsBuilder
     */
    public function customize($callback)
    {
        array_map($callback, $this->getRouteConfigs());

        return $this;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig[]
     */
    public function getTestCaseConfigs()
    {
        $testCaseConfigs = [];
        foreach ($this->getRouteConfigs() as $routeConfig) {
            $testCaseConfigs = array_merge($testCaseConfigs, $routeConfig->getTestCaseConfigs());
        }

        return $testCaseConfigs;
    }
}
