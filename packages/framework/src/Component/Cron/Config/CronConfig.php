<?php

namespace Shopsys\FrameworkBundle\Component\Cron\Config;

use DateTimeInterface;
use Shopsys\FrameworkBundle\Component\Cron\CronTimeResolver;
use Shopsys\Plugin\Cron\IteratedCronModuleInterface;
use Shopsys\Plugin\Cron\SimpleCronModuleInterface;

class CronConfig
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronTimeResolver
     */
    protected $cronTimeResolver;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    protected $cronModuleConfigs;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronTimeResolver $cronTimeResolver
     */
    public function __construct(CronTimeResolver $cronTimeResolver)
    {
        $this->cronTimeResolver = $cronTimeResolver;
        $this->cronModuleConfigs = [];
    }

    /**
     * @param \Shopsys\Plugin\Cron\SimpleCronModuleInterface|\Shopsys\Plugin\Cron\IteratedCronModuleInterface $service
     * @param string $serviceId
     * @param string $timeHours
     * @param string $timeMinutes
     *
     * @deprecated Use `registerCronModuleInstance` instead
     */
    public function registerCronModule($service, $serviceId, $timeHours, $timeMinutes)
    {
        $this->registerCronModuleInstance($service, $serviceId, $timeHours, $timeMinutes, CronModuleConfig::DEFAULT_INSTANCE_NAME);
    }

    /**
     * @param \Shopsys\Plugin\Cron\SimpleCronModuleInterface|\Shopsys\Plugin\Cron\IteratedCronModuleInterface $service
     * @param string $serviceId
     * @param string $timeHours
     * @param string $timeMinutes
     * @param string $instanceName
     */
    public function registerCronModuleInstance($service, string $serviceId, string $timeHours, string $timeMinutes, string $instanceName): void
    {
        if (!$service instanceof SimpleCronModuleInterface && !$service instanceof IteratedCronModuleInterface) {
            throw new \Shopsys\FrameworkBundle\Component\Cron\Exception\InvalidCronModuleException($serviceId);
        }
        $this->cronTimeResolver->validateTimeString($timeHours, 23, 1);
        $this->cronTimeResolver->validateTimeString($timeMinutes, 55, 5);

        $cronModuleConfig = new CronModuleConfig($service, $serviceId, $timeHours, $timeMinutes);
        $cronModuleConfig->assignToInstance($instanceName);

        $this->cronModuleConfigs[] = $cronModuleConfig;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getAllCronModuleConfigs()
    {
        return $this->cronModuleConfigs;
    }

    /**
     * @param \DateTimeInterface $roundedTime
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
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
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig
     */
    public function getCronModuleConfigByServiceId($serviceId)
    {
        foreach ($this->cronModuleConfigs as $cronConfig) {
            if ($cronConfig->getServiceId() === $serviceId) {
                return $cronConfig;
            }
        }

        throw new \Shopsys\FrameworkBundle\Component\Cron\Config\Exception\CronModuleConfigNotFoundException($serviceId);
    }

    /**
     * @param string $instanceName
     * @return array
     */
    public function getCronModuleConfigsForInstance(string $instanceName): array
    {
        $matchedCronConfigs = [];

        foreach ($this->cronModuleConfigs as $cronModuleConfig) {
            if ($cronModuleConfig->getInstanceName() === $instanceName) {
                $matchedCronConfigs[] = $cronModuleConfig;
            }
        }

        return $matchedCronConfigs;
    }
}
