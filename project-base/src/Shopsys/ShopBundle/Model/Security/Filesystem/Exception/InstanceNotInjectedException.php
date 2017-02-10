<?php

namespace Shopsys\ShopBundle\Model\Security\Filesystem\Exception;

use Exception;
use Shopsys\ShopBundle\Model\Security\Filesystem\Exception\FilesystemException;

class InstanceNotInjectedException extends Exception implements FilesystemException
{
    /**
     * @param string $message
     * @param \Exception|null $previous
     */
    public function __construct($message = '', Exception $previous = null) {
        parent::__construct($message, 0, $previous);
    }
}
