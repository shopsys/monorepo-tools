<?php

namespace Shopsys\ShopBundle\Model\Pricing\Currency\Exception;

use Exception;

class DeletingNotAllowedToDeleteCurrencyException extends Exception implements CurrencyException
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
