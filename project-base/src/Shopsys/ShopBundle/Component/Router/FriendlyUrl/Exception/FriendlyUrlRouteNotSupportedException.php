<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;

class FriendlyUrlRouteNotSupportedException extends Exception implements FriendlyUrlException
{
    /**
     * @param string $routeName
     */
    public function __construct($routeName)
    {
        parent::__construct('Generating friendly URL for route "' . $routeName . '" is not yet supported.');
    }
}
