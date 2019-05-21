<?php

namespace Shopsys\FrameworkBundle\Model\Cart\Exception;

use Exception;

class CartIsEmptyException extends Exception implements CartException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct(?Exception $previous = null)
    {
        parent::__construct('Cart is empty.', 0, $previous);
    }
}
