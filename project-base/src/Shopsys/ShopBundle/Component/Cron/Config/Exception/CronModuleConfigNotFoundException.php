<?php

namespace Shopsys\ShopBundle\Component\Cron\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Cron\Config\Exception\CronConfigException;

class CronModuleConfigNotFoundException extends Exception implements CronConfigException
{
    /**
     * @param string $moduleId
     * @param \Exception $previous
     */
    public function __construct($moduleId, Exception $previous = null) {
        parent::__construct('Cron module config with module ID "' . $moduleId . '" not found.', 0, $previous);
    }
}
