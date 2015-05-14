<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTime;
use SS6\ShopBundle\Component\Cron\Config\CronConfig;

class CronFacade {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\Config\CronConfig
	 */
	private $cronConfig;

	public function __construct(CronConfig $cronConfig) {
		$this->cronConfig = $cronConfig;
	}

	/**
	 * @param \DateTime $roundedTime
	 */
	public function runServicesForTime(DateTime $roundedTime) {
		foreach ($this->cronConfig->getCronServiceConfigsByTime($roundedTime) as $cronServiceConfig) {
			$cronServiceConfig->getCronService()->run();
		}
	}
}
