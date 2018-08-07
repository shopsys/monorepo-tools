<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

class CronModuleFactory implements CronModuleFactoryInterface
{
    /**
     * @param string $serviceId
     * @return \Shopsys\FrameworkBundle\Component\Cron\CronModule
     */
    public function create(string $serviceId): CronModule
    {
        return new CronModule($serviceId);
    }
}
