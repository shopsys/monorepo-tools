<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

/**
 * SimpleCronModuleInterface is the interface that all simple CRON modules must implemement.
 *
 * In order for your CRON module to be run you must register it in cron.yml config file.
 * Module is run every time the current system time matches the mask specified in cron.yml.
 * The module should not take more than one minute to run. If you want to process longer
 * taking tasks @see \Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface.
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
