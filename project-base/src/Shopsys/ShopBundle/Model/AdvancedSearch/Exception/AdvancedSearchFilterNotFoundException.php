<?php

namespace Shopsys\ShopBundle\Model\AdvancedSearch\Exception;

use Exception;
use Shopsys\ShopBundle\Model\AdvancedSearch\Exception\AdvancedSearchException;

class AdvancedSearchFilterNotFoundException extends Exception implements AdvancedSearchException
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
