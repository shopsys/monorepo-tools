<?php

namespace Shopsys\ShopBundle\Component\Cron;

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
