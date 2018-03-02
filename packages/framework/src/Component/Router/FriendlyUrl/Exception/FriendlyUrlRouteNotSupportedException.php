<?php

namespace Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\Exception;

use Exception;

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
