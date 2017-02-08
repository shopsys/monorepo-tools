<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use SS6\ShopBundle\Component\Cron\CronModuleInterface;
use SS6\ShopBundle\Component\Cron\CronTimeInterface;
use SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface;

class CronModuleConfig implements CronTimeInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronModuleInterface
	 */
	private $cronModuleService;

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

	// @codingStandardsIgnoreStart
	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronModuleInterface|\SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface $cronModuleService
	 * @param string $moduleId
	 * @param string $timeHours
	 * @param string $timeMinutes
	 */
	public function __construct($cronModuleService, $moduleId, $timeHours, $timeMinutes) {
		// @codingStandardsIgnoreEnd
		if (
			!$cronModuleService instanceof CronModuleInterface
			&& !$cronModuleService instanceof IteratedCronModuleInterface
		) {
			throw new \SS6\ShopBundle\Component\Cron\Exception\InvalidCronModuleException($moduleId);
		}
		$this->cronModuleService = $cronModuleService;
		$this->moduleId = $moduleId;
		$this->timeHours = $timeHours;
		$this->timeMinutes = $timeMinutes;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Cron\CronModuleInterface|\SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface
	 */
	public function getCronModuleService() {
		return $this->cronModuleService;
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
