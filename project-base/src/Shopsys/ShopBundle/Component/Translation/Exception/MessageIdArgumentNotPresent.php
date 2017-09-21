<?php

namespace Shopsys\ShopBundle\Component\Translation\Exception;

use Exception;

class MessageIdArgumentNotPresent extends Exception implements TranslationException
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
