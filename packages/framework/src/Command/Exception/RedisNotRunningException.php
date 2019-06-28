<?php

namespace Shopsys\FrameworkBundle\Command\Exception;

use Exception;
use Throwable;

/**
 * @deprecated This exception is deprecated since SSFW 7.3
 */
class RedisNotRunningException extends Exception
{
    /**
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
