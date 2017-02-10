<?php

namespace Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Router\FriendlyUrl\Exception\FriendlyUrlException;

class MethodGenerateIsNotSupportedException extends Exception implements FriendlyUrlException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
