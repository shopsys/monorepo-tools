<?php

namespace Shopsys\ShopBundle\Component\Cron\Config\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Cron\Config\Exception\CronConfigException;

class CronModuleNotFoundException extends Exception implements CronConfigException {

    /**
     * @param string $moduleId
     * @param \Exception $previous
     */
    public function __construct($moduleId, Exception $previous = null) {
        parent::__construct('Cron module with ID "' . $moduleId . '" not found.', 0, $previous);
    }
}
