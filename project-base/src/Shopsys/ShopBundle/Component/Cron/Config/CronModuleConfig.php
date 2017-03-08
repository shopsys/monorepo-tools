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
    private $cronModuleService;

    /**
     * @var string
     */
    private $moduleId;

    /**
     * @var string
     */
    private $timeMinutes;

    /**
     * @var string
     */
    private $timeHours;

    // @codingStandardsIgnoreStart
    /**
     * @param \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface $cronModuleService
     * @param string $moduleId
     * @param string $timeHours
     * @param string $timeMinutes
     */
    public function __construct($cronModuleService, $moduleId, $timeHours, $timeMinutes)
    {
        // @codingStandardsIgnoreEnd
        if (!$cronModuleService instanceof SimpleCronModuleInterface
            && !$cronModuleService instanceof IteratedCronModuleInterface
        ) {
            throw new \Shopsys\ShopBundle\Component\Cron\Exception\InvalidCronModuleException($moduleId);
        }
        $this->cronModuleService = $cronModuleService;
        $this->moduleId = $moduleId;
        $this->timeHours = $timeHours;
        $this->timeMinutes = $timeMinutes;
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface
     */
    public function getCronModuleService()
    {
        return $this->cronModuleService;
    }

    /**
     * @return string
     */
    public function getModuleId()
    {
        return $this->moduleId;
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
