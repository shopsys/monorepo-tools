<?php

namespace Shopsys\ShopBundle\Component\Doctrine\Cache\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Doctrine\Cache\Exception\DoctrineCacheException;

class InvalidArgumentException extends Exception implements DoctrineCacheException
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
