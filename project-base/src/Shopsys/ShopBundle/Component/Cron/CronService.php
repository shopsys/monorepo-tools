<?php

namespace SS6\ShopBundle\Component\Cron;

class CronService {

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
	 * @param string[] $scheduledCronModuleIds
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[]
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
