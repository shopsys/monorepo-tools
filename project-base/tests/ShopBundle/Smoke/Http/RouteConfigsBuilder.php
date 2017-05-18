<?php

namespace Tests\ShopBundle\Smoke\Http;

class RouteConfigsBuilder
{
    /**
     * @var \Tests\ShopBundle\Smoke\Http\RouteConfig[]|null
     */
    private $routeConfigs;

    public function __construct(array $routeConfigs)
    {
        $this->routeConfigs = $routeConfigs;
    }

    /**
     * @param callable $callback
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfigsBuilder
     */
    public function customize($callback)
    {
        array_map($callback, $this->routeConfigs);

        return $this;
    }

    /**
     * @return \Tests\ShopBundle\Smoke\Http\TestCaseConfig[]
     */
    public function getTestCaseConfigs()
    {
        $testCaseConfigs = [];
        foreach ($this->routeConfigs as $routeConfig) {
            $testCaseConfigs = array_merge($testCaseConfigs, $routeConfig->generateTestCaseConfigs());
        }

        return $testCaseConfigs;
    }
}
