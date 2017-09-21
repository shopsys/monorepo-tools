<?php

namespace Shopsys\ShopBundle\Component\Breadcrumb\Exception;

use Exception;

class UnableToGenerateBreadcrumbItemsException extends Exception implements BreadcrumbException
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
