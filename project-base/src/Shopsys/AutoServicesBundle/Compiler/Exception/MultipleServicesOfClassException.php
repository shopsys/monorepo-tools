<?php

namespace Shopsys\AutoServicesBundle\Compiler\Exception;

use Exception;
use Shopsys\AutoServicesBundle\Compiler\Exception\CompilerException;

class MultipleServicesOfClassException extends Exception implements CompilerException
{

    /**
     * @param string $className
     * @param string[] $serviceIds
     * @param \Exception|null $previous
     */
    public function __construct($className, $serviceIds, Exception $previous = null) {
        $serviceNames = implode(', ', $serviceIds);
        $message = 'Multiple services of ' . $className . ' defined (' . $serviceNames . ')';
        parent::__construct($message, 0, $previous);
    }
}
