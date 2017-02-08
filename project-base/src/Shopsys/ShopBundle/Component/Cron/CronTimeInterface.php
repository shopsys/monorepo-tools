<?php

namespace SS6\ShopBundle\Component\Cron;

interface CronTimeInterface {

	/**
	 * @return string
	 */
	public function getTimeMinutes();

	/**
	 * @return string
	 */
	public function getTimeHours();
}
