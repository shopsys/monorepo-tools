<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception;

use Exception;

class FriendlyUrlRouteNotFoundException extends Exception implements FriendlyUrlException
{
    /**
     * @param string $routeName
     * @param string $routerResourceFilepath
     */
    public function __construct($routeName, $routerResourceFilepath)
    {
        parent::__construct(
            sprintf('Friendly URL route "%s" not found in "%s".', $routeName, realpath($routerResourceFilepath))
        );
    }
}
