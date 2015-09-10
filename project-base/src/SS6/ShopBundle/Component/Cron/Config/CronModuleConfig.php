<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Component\Cron\CronTimeInterface;

class CronModuleConfig implements CronTimeInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronModuleInterface
	 */
	private $cronModule;

	/**
	 * @var string
	 */
	private $moduleId;

	/**
	 * @var string
	 */
	private $timeMinutes;

	/**
	 * @var string
	 */
	private $timeHours;

	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronModuleInterface $cronModule
	 * @param string $moduleId
	 * @param string $timeMinutes
	 * @param string $timeHours
	 */
	public function __construct(CronModuleInterface $cronModule, $moduleId, $timeMinutes, $timeHours) {
		$this->cronModule = $cronModule;
		$this->moduleId = $moduleId;
		$this->timeMinutes = $timeMinutes;
		$this->timeHours = $timeHours;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Cron\CronModuleInterface
	 */
	public function getCronModule() {
		return $this->cronModule;
	}

	/**
	 * @return string
	 */
	public function getModuleId() {
		return $this->moduleId;
	}

	/**
	 * @return string
	 */
	public function getTimeMinutes() {
		return $this->timeMinutes;
	}

	/**
	 * @return string
	 */
	public function getTimeHours() {
		return $this->timeHours;
	}

}
