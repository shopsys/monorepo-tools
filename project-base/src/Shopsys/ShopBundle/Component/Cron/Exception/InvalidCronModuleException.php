<?php

namespace Shopsys\ShopBundle\Component\Cron\Exception;

use Exception;
use Shopsys\ShopBundle\Component\Cron\Exception\CronException;

class InvalidCronModuleException extends Exception implements CronException
{
    /**
     * @param string $moduleId
     * @param \Exception|null $previous
     */
    public function __construct($moduleId, Exception $previous = null) {
        parent::__construct('Module "' . $moduleId . '" does not have valid interface.', 0, $previous);
    }
}
