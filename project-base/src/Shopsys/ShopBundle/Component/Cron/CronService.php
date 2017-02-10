<?php

namespace Shopsys\ShopBundle\Component\Cron;

class CronService {

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
     * @param string[] $scheduledCronModuleIds
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function filterScheduledCronModuleConfigs(array $cronModulesConfigs, array $scheduledCronModuleIds) {
        foreach ($cronModulesConfigs as $key => $cronModulesConfig) {
            if (!in_array($cronModulesConfig->getModuleId(), $scheduledCronModuleIds, true)) {
                unset($cronModulesConfigs[$key]);
            }
        }

        return $cronModulesConfigs;
    }

}
