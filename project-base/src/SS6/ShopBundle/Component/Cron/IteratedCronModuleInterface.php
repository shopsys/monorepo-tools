<?php

namespace SS6\ShopBundle\Component\Cron;

use SS6\ShopBundle\Component\Cron\LoggingCronModuleInterface;

interface IteratedCronModuleInterface extends LoggingCronModuleInterface {

	/**
	 * @return bool
	 */
	public function iterate();
}
