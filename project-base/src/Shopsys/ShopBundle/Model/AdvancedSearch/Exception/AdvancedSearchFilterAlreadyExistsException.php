<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch\Exception;

use Exception;

class AdvancedSearchFilterAlreadyExistsException extends Exception implements AdvancedSearchException
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
