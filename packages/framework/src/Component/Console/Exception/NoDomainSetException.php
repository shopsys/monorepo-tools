<?php

namespace Shopsys\FrameworkBundle\Component\Console\Exception;

use Exception;

class NoDomainSetException extends Exception implements ConsoleException
{
    /**
     * @param \Exception|null $previous
     */
    public function __construct(Exception $previous = null)
    {
        $message = 'There are no domains set.';
        parent::__construct($message, 0, $previous);
    }
}
