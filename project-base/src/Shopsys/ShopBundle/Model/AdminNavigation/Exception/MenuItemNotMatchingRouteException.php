<?php

namespace Shopsys\ShopBundle\Model\AdminNavigation\Exception;

use Exception;

class MenuItemNotMatchingRouteException extends Exception implements MenuException
{
    /**
     * @param string $route
     * @param array|null $parameters
     * @param \Exception|null $previous
     */
    public function __construct($route, $parameters, Exception $previous = null)
    {
        $message = 'Menu item for route "' . $route . '" and parameters "' . var_export($parameters, true) . '" not found. '
            . 'Maybe you forgot to add it to "admin_menu.yml".';
        parent::__construct($message, 0, $previous);
    }
}
