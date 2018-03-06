<?php

namespace Shopsys\FrameworkBundle\Model\Administrator\Exception;

use Exception;

class DuplicateUserNameException extends Exception implements AdministratorException
{
    /**
     * @param string $username
     * @param \Exception|null $previous
     */
    public function __construct($username, Exception $previous = null)
    {
        parent::__construct('Administrator with user name ' . $username . ' already exists.', 0, $previous);
    }
}
