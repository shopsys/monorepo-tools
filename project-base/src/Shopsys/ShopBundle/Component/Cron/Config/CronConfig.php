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

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\CronTimeResolver $cronTimeResolver
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     */
    public function __construct(
        CronTimeResolver $cronTimeResolver,
        array $cronModuleConfigs
    ) {
        $this->cronModuleConfigs = $cronModuleConfigs;
        $this->cronTimeResolver = $cronTimeResolver;
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
