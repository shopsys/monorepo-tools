<?php

namespace Shopsys\FrameworkBundle\Component\System\Exception;

use Exception;
use Shopsys\FrameworkBundle\Component\System\PostgresqlLocaleMapper;
use Throwable;

class UnknownWindowsLocaleException extends Exception
{
    public function __construct($collation, Throwable $previous = null)
    {
        $message = sprintf(
            'Matching Windows locale for collation "%s" is not known. Consider updating %s class.',
            $collation,
            PostgresqlLocaleMapper::class
        );

        parent::__construct($message, 0, $previous);
    }
}
