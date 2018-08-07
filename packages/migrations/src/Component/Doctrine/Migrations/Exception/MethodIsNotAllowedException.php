<?php

namespace Shopsys\MigrationBundle\Component\Doctrine\Migrations\Exception;

use Exception;

class MethodIsNotAllowedException extends Exception
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
