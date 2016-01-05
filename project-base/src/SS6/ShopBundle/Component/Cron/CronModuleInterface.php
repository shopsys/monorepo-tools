<?php

namespace SS6\ShopBundle\Component\Cron;

use SS6\ShopBundle\Component\Cron\LoggingCronModuleInterface;

interface CronModuleInterface extends LoggingCronModuleInterface {

	public function run();

}
