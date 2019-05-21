<?php

namespace Shopsys\FrameworkBundle\Component\Grid\InlineEdit\Exception;

use Exception;

class InvalidServiceException extends Exception implements InlineEditException
{
    /**
     * @param string $serviceName
     * @param \Exception|null $previous
     */
    public function __construct($serviceName, ?Exception $previous = null)
    {
        $message = 'Service with name "' . $serviceName . '" does not exist or not implement necessary interface.';
        parent::__construct($message, 0, $previous);
    }
}
