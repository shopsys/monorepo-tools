<?php

namespace Shopsys\ShopBundle\Component\Cron\Config;

use DateTimeInterface;
use Shopsys\ShopBundle\Component\Cron\CronTimeResolver;
use Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface;
use Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface;

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
     * @param \Shopsys\ShopBundle\Component\Cron\SimpleCronModuleInterface|\Shopsys\ShopBundle\Component\Cron\IteratedCronModuleInterface $service
     * @param string $serviceId
     * @param string $timeHours
     * @param string $timeMinutes
     */
    public function registerCronModule($service, $serviceId, $timeHours, $timeMinutes)
    {
        if (!$service instanceof SimpleCronModuleInterface && !$service instanceof IteratedCronModuleInterface) {
            throw new \Shopsys\ShopBundle\Component\Cron\Exception\InvalidCronModuleException($serviceId);
        }
        $this->cronTimeResolver->validateTimeString($timeHours, 23, 1);
        $this->cronTimeResolver->validateTimeString($timeMinutes, 55, 5);

        $this->cronModuleConfigs[] = new CronModuleConfig($service, $serviceId, $timeHours, $timeMinutes);
    }

    /**
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getAllCronModuleConfigs()
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
     * @param string $serviceId
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig
     */
    public function getCronModuleConfigByServiceId($serviceId)
    {
        foreach ($this->cronModuleConfigs as $cronConfig) {
            if ($cronConfig->getServiceId() === $serviceId) {
                return $cronConfig;
            }
        }

        throw new \Shopsys\ShopBundle\Component\Cron\Config\Exception\CronModuleConfigNotFoundException($serviceId);
    }
}
