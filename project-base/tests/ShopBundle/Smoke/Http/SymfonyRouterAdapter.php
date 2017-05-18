<?php

namespace Tests\ShopBundle\Smoke\Http;

use Symfony\Component\Routing\RouterInterface;
use Tests\ShopBundle\Smoke\Http\RouteConfig;

class SymfonyRouterAdapter implements RouterAdapterInterface
{
    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

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
    public function getRouteConfigs()
    {
        $routeConfigs = [];
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            $routeConfigs[] = new RouteConfig($routeName, $route);
        }

        return $routeConfigs;
    }

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     */
    public function generateUri(TestCaseConfig $config)
    {
        return $this->router->generate($config->getRouteName(), $config->getParameters());
    }
}
