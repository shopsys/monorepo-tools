<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;

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
