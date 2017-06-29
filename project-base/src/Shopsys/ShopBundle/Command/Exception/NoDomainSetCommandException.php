<?php

namespace Shopsys\ShopBundle\Command\Exception;

use Exception;
use Shopsys\ShopBundle\Command\Exception\CommandException;

class NoDomainSetCommandException extends Exception implements CommandException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct(Exception $previous = null)
    {
        $message = 'There are no domains set.';
        parent::__construct($message, 0, $previous);
    }
}
