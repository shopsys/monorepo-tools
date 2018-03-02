<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Security\Exception;

use Exception;

class AdministratorIsNotLoggedException extends Exception implements SecurityException
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
