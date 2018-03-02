<?php

namespace Shopsys\FrameworkBundle\Model\Pricing\Vat\Exception;

use Exception;

class VatMarkedAsDeletedDeleteException extends Exception implements VatException
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
