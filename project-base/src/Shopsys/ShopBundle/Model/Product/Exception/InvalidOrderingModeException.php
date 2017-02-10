<?php

namespace Shopsys\ShopBundle\Model\Product\Exception;

use Exception;

class InvalidOrderingModeException extends Exception implements ProductException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
