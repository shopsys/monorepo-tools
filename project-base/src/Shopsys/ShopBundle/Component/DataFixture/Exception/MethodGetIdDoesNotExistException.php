<?php

namespace Shopsys\FrameworkBundle\Component\DataFixture\Exception;

use Exception;

class MethodGetIdDoesNotExistException extends Exception implements DataFixtureException
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
