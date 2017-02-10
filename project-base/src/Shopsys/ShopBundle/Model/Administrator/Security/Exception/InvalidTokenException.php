<?php

namespace Shopsys\ShopBundle\Model\Administrator\Security\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Administrator\Security\Exception\SecurityException;

class InvalidTokenException extends Exception implements SecurityException
{

    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }

}
