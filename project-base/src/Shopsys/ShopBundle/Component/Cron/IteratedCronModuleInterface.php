<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

/**
 * IteratedCronModuleInterface is the interface that all long-running CRON modules must implmement.
 *
 * In order for your CRON module to be run you must register it in services_cron.yml config file.
 * Module is started every time the current system time matches the mask specified in services_cron.yml.
 * If the module takes too long to run it will be suspended by sleep() method and will be woken up
 * and re-run next time regardless of the current system time.
 * If you want to process a short task that does not take more than one minute use
 * @see \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface.
 */
interface IteratedCronModuleInterface
{
    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger);

    /**
     * Restores the module's state after being suspended.
     *
     * If the CRON module was suspended before, this method is called before any calls of iterate() method.
     * You should restore CRON module internal state that was previously stored in sleep() method.
     */
    public function wakeUp();

    /**
     * Runs one iteration of long-running task.
     *
     * This method is called to process a single part of the whole work that the CRON module does.
     * The method should return TRUE if there is any work left of FALSE when it finished everything.
     *
     * @return bool
     */
    public function iterate();

    /**
     * Suspends the process to be re-run later.
     *
     * This method is called if the CRON module did not finish its work yet but there is no more time to run
     * another iteration.
     * Here you should save module's internal state that should be restored on next wakeUp() call.
     */
    public function sleep();
}
