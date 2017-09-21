<?php

namespace Shopsys\ShopBundle\Component\Cron\Config\Exception;

use Exception;

class CronModuleConfigNotFoundException extends Exception implements CronConfigException
{
    /**
     * @param string $serviceId
     * @param \Exception $previous
     */
    public function __construct($serviceId, Exception $previous = null)
    {
        parent::__construct('Cron module config with service ID "' . $serviceId . '" not found.', 0, $previous);
    }
}
