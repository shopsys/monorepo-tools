<?php

namespace ShopBundle\Component\Error\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Error\Exception\ErrorException;

class BadErrorPageStatusCodeException extends Exception implements ErrorException
{
    /**
     * @param string $url
     * @param int $expectedStatusCode
     * @param int $actualStatusCode
     * @param \Exception|null $previous
     */
    public function __construct($url, $expectedStatusCode, $actualStatusCode, Exception $previous = null) {
        $message = sprintf(
            'Error page "%s" has "%s" status code, expects "%s".',
            $url,
            $actualStatusCode,
            $expectedStatusCode
        );

        parent::__construct($message, 0, $previous);
    }
}
