<?php

namespace Tests\ShopBundle\Smoke\Http;

interface RouterAdapterInterface
{
    /**
     * @return \Tests\ShopBundle\Smoke\Http\RouteConfig[]
     */
    public function getRouteConfigs();

    /**
     * @param \Tests\ShopBundle\Smoke\Http\RequestDataSet $requestDataSet
     * @return string
     */
    public function generateUri(RequestDataSet $requestDataSet);
}
