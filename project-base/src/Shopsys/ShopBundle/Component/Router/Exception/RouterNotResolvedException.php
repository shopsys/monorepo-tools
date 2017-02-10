<?php

namespace Shopsys\ShopBundle\Component\Router\Exception;

use Exception;

class RouterNotResolvedException extends Exception implements RouterException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
