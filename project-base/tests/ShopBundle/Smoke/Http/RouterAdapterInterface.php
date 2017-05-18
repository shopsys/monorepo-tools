<?php

namespace Tests\ShopBundle\Smoke\Http;

interface RouterAdapterInterface
{
    /**
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig[]
     */
    public function getRouteConfigs();

    /**
     * @param \Tests\ShopBundle\Smoke\Http\TestCaseConfig $config
     * @return string
     */
    public function generateUri(TestCaseConfig $config);
}
