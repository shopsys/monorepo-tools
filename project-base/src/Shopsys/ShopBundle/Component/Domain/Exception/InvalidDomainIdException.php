<?php

namespace Shopsys\ShopBundle\Component\Domain\Exception;

use Exception;

class InvalidDomainIdException extends Exception implements DomainException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
