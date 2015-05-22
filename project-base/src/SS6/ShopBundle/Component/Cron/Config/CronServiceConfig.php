<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use SS6\ShopBundle\Component\Cron\CronServiceInterface;
use SS6\ShopBundle\Component\Cron\CronTimeInterface;

class CronServiceConfig implements CronTimeInterface {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronServiceInterface
	 */
	private $cronService;

	/**
	 * @var string
	 */
	private $timeMinutes;

	/**
	 * @var string
	 */
	private $timeHours;

	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronServiceInterface $cronService
	 * @param string $timeMinutes
	 * @param string $timeHours
	 */
	public function __construct(CronServiceInterface $cronService, $timeMinutes, $timeHours) {
		$this->cronService = $cronService;
		$this->timeMinutes = $timeMinutes;
		$this->timeHours = $timeHours;
	}

	/**
	 * @return string
	 */
	public function getCronService() {
		return $this->cronService;
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
