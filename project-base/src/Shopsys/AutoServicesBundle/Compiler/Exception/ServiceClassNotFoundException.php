<?php

namespace Shopsys\AutoServicesBundle\Compiler\Exception;

use Exception;
use Shopsys\AutoServicesBundle\Compiler\Exception\CompilerException;

class ServiceClassNotFoundException extends Exception implements CompilerException
{
    /**
     * @param string $className
     * @param \Exception|null $previous
     */
    public function __construct($className, $previous = null)
    {
        parent::__construct('Service for class "' . $className . '" can not be resolved.', 0, $previous);
    }
}
