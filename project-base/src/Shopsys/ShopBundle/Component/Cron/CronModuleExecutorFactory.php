<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Shopsys\ShopBundle\Component\Cron\CronModuleExecutor;

class CronModuleExecutorFactory
{
    /**
     * @param int $secondsTimeout
     * @return \Shopsys\ShopBundle\Component\Cron\CronModuleExecutor
     */
    public function create($secondsTimeout)
    {
        return new CronModuleExecutor($secondsTimeout);
    }
}
