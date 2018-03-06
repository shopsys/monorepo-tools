<?php

namespace Shopsys\FrameworkBundle\Component\ConfirmDelete\Exception;

use Exception;

class InvalidEntityPassedException extends Exception implements ConfirmDeleteException
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
