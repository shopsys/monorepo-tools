<?php

namespace Shopsys\Plugin\Cron;

use Symfony\Bridge\Monolog\Logger;

/**
 * SimpleCronModuleInterface is the interface that all simple CRON modules must implement.
 *
 * In order for your CRON module to be run you must register it as a service tagged as "shopsys.cron".
 * Module is started every time the current system time matches the mask specified in tag attributes
 * named "hours" and "minutes" (e.g. hours: "*", minutes: "0,30" for running every half hour).
 * Module is run every time the current system time matches the mask specified in cron.yml.
 * The module should not take more than one minute to run. If you want to process longer
 * taking tasks @see \Shopsys\Plugin\Cron\IteratedCronModuleInterface.
 */
interface SimpleCronModuleInterface
{
    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger);

    /**
     * This method is called to run the CRON module.
     */
    public function run();
}
