<?php

namespace Shopsys\ShopBundle\Component\Cron;

class CronService
{
    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     * @param string[] $scheduledServiceIds
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
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
