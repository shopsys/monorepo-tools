<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use SS6\ShopBundle\Component\Cron\Config\CronConfig;
use SS6\ShopBundle\Component\Cron\Config\CronConfigLoader;
use SS6\ShopBundle\Component\Cron\CronTimeResolver;

class CronConfigFactory {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronTimeResolver
	 */
	private $cronTimeResolver;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\Config\CronConfigLoader
	 */
	private $cronConfigLoader;

	public function __construct(CronTimeResolver $cronTimeResolver, CronConfigLoader $cronConfigLoader) {
		$this->cronConfigLoader = $cronConfigLoader;
		$this->cronTimeResolver = $cronTimeResolver;
	}

	/**
	 * @param string $ymlFilepath
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronConfig
	 */
	public function create($ymlFilepath) {
		$cronModuleConfigs = $this->cronConfigLoader->loadCronModuleConfigsFromYaml($ymlFilepath);

		return new CronConfig($this->cronTimeResolver, $cronModuleConfigs);
	}

}
