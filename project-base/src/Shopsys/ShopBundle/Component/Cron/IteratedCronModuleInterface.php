<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

interface IteratedCronModuleInterface {

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger);

    public function sleep();

    public function wakeUp();

    /**
     * @return bool
     */
    public function iterate();
}
