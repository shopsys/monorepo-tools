<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use Shopsys\ShopBundle\Component\Cron\CronTimeInterface;
use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface;

class CronModuleConfig implements CronTimeInterface
{
    /**
     * @var \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface
     */
    private $service;

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $timeMinutes;

    /**
     * @var string
     */
    private $timeHours;

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface $service
     * @param string $serviceId
     * @param string $timeHours
     * @param string $timeMinutes
     */
    public function __construct($service, $serviceId, $timeHours, $timeMinutes)
    {
        $this->service = $service;
        $this->serviceId = $serviceId;
        $this->timeHours = $timeHours;
        $this->timeMinutes = $timeMinutes;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @return string
     */
    public function getTimeMinutes()
    {
        return $this->timeMinutes;
    }

    /**
     * @return string
     */
    public function getTimeHours()
    {
        return $this->timeHours;
    }
}
