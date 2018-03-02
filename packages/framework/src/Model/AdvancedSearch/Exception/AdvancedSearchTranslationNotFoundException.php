<?php

namespace Shopsys\FrameworkBundle\Model\AdvancedSearch\Exception;

use Exception;

class AdvancedSearchTranslationNotFoundException extends Exception implements AdvancedSearchException
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
