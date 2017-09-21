<?php

namespace Shopsys\ShopBundle\Model\Cart\Exception;

use Exception;

class InvalidCartItemException extends Exception implements CartException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
