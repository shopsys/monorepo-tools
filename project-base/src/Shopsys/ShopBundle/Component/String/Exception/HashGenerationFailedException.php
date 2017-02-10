<?php

namespace Shopsys\ShopBundle\Component\String\Exception;

use Exception;

class HashGenerationFailedException extends Exception implements StringException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
