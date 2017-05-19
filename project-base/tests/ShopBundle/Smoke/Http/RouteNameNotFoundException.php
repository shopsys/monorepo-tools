<?php

namespace Tests\ShopBundle\Smoke\Http;

use Exception;

class RouteNameNotFoundException extends Exception
{
    /**
     * @param string|string[] $routeName
     */
    public function __construct($routeName)
    {
        $routeNames = (array)$routeName;

        parent::__construct(
            'Route name' . (count($routeNames) !== 1 ? 's' : '') . ' "' . implode('", "', $routeNames) . '" not found!'
        );
    }
}
