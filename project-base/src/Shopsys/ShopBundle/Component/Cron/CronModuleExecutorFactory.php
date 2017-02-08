<?php

namespace SS6\ShopBundle\Component\Cron;

use SS6\ShopBundle\Component\Cron\CronModuleExecutor;

class CronModuleExecutorFactory {

	/**
	 * @param int $secondsTimeout
	 * @return \SS6\ShopBundle\Component\Cron\CronModuleExecutor
	 */
	public function create($secondsTimeout) {
		return new CronModuleExecutor($secondsTimeout);
	}

}
