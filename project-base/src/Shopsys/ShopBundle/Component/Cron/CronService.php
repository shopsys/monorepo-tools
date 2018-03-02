<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

class CronService
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     * @param string[] $scheduledServiceIds
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function filterScheduledCronModuleConfigs(array $cronModuleConfigs, array $scheduledServiceIds)
    {
        foreach ($cronModuleConfigs as $key => $cronModuleConfig) {
            if (!in_array($cronModuleConfig->getServiceId(), $scheduledServiceIds, true)) {
                unset($cronModuleConfigs[$key]);
            }
        }

        return $cronModuleConfigs;
    }
}
