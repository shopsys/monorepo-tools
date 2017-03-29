<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use DateTimeInterface;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;

class CronConfig
{
    /**
     * @var \Shopsys\ShopBundle\Component\Cron\CronTimeResolver
     */
    private $cronTimeResolver;

    /**
     * @var \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    private $cronModuleConfigs;

    public function __construct(CronTimeResolver $cronTimeResolver)
    {
        $this->cronTimeResolver = $cronTimeResolver;
        $this->cronModuleConfigs = [];
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface $cronModuleService
     * @param string $moduleId
     * @param string $timeHours
     * @param string $timeMinutes
     */
    public function registerCronModule($cronModuleService, $moduleId, $timeHours, $timeMinutes)
    {
        $this->cronTimeResolver->validateTimeString($timeHours, 23, 1);
        $this->cronTimeResolver->validateTimeString($timeMinutes, 55, 5);

        $this->cronModuleConfigs[] = new CronModuleConfig($cronModuleService, $moduleId, $timeHours, $timeMinutes);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getAll()
    {
        return $this->cronModuleConfigs;
    }

    /**
     * @param \DateTimeInterface $roundedTime
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getCronModuleConfigsByTime(DateTimeInterface $roundedTime)
    {
        $matchedCronConfigs = [];

        foreach ($this->cronModuleConfigs as $cronConfig) {
            if ($this->cronTimeResolver->isValidAtTime($cronConfig, $roundedTime)) {
                $matchedCronConfigs[] = $cronConfig;
            }
        }

        return $matchedCronConfigs;
    }

    /**
     * @param string $moduleId
     */
    public function getCronModuleConfigByModuleId($moduleId)
    {
        foreach ($this->cronModuleConfigs as $cronConfig) {
            if ($cronConfig->getModuleId() === $moduleId) {
                return $cronConfig;
            }
        }

        throw new \Shopsys\ShopBundle\Component\Cron\Config\Exception\CronModuleConfigNotFoundException($moduleId);
    }
}
