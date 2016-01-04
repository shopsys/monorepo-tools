<?php

namespace SS6\ShopBundle\Component\Cron;

use SS6\ShopBundle\Component\Cron\LoggingCronModuleInterface;

interface IteratedCronModuleInterface extends LoggingCronModuleInterface {

	public function sleep();

	public function wakeUp();

	/**
	 * @return bool
	 */
	public function iterate();
}
