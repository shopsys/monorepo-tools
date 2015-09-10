<?php

namespace SS6\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

interface CronModuleInterface {

	/**
	 * @param \Symfony\Bridge\Monolog\Logger $logger
	 */
	public function run(Logger $logger);
}
