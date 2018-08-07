<?php

namespace Shopsys\MigrationBundle\Command\Exception;

use Exception;

class MigrateCommandException extends Exception
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
