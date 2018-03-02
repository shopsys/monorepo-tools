<?php

namespace Shopsys\FrameworkBundle\Component\Entity\Exception;

use Exception;

class UnexpectedTypeException extends Exception implements EntityException
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
