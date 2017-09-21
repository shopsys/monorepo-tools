<?php

namespace Shopsys\ShopBundle\Component\Cron\Exception;

use Exception;

class InvalidCronModuleException extends Exception implements CronException
{
    /**
     * @param string $serviceId
     * @param \Exception|null $previous
     */
    public function __construct($serviceId, Exception $previous = null)
    {
        parent::__construct('Module "' . $serviceId . '" does not have valid interface.', 0, $previous);
    }
}
