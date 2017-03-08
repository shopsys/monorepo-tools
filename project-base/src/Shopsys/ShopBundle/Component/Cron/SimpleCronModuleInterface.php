<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Symfony\Bridge\Monolog\Logger;

interface SimpleCronModuleInterface
{
    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     */
    public function setLogger(Logger $logger);

    public function run();
}
