<?php

namespace Shopsys\FrameworkBundle\Model\Heureka\Exception;

use Exception;

class LocaleNotSupportedException extends Exception implements HeurekaException
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
