<?php

namespace Shopsys\FrameworkBundle\Component\Error\Exception;

use Exception;

class ErrorPageNotFoundException extends Exception implements ErrorException
{
    /**
     * @param int $domainId
     * @param int $statusCode
     * @param \Exception|null $previous
     */
    public function __construct($domainId, $statusCode, Exception $previous = null)
    {
        $message = 'Error page with status code "' . $statusCode . '" on domain with id "' . $domainId . '" not found.';

        parent::__construct($message, 0, $previous);
    }
}
