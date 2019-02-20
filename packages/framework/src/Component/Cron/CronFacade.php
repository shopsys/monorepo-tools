<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use DateTimeInterface;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronConfig;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig;
use Symfony\Bridge\Monolog\Logger;

class CronFacade
{
    const TIMEOUT_SECONDS = 4 * 60;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\Config\CronConfig
     */
    protected $cronConfig;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronModuleFacade
     */
    protected $cronModuleFacade;

    /**
     * @param \Symfony\Bridge\Monolog\Logger $logger
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronConfig $cronConfig
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronModuleFacade $cronModuleFacade
     */
    public function __construct(
        Logger $logger,
        CronConfig $cronConfig,
        CronModuleFacade $cronModuleFacade
    ) {
        $this->logger = $logger;
        $this->cronConfig = $cronConfig;
        $this->cronModuleFacade = $cronModuleFacade;
    }

    /**
     * @param \DateTimeInterface $roundedTime
     */
    public function scheduleModulesByTime(DateTimeInterface $roundedTime)
    {
        $cronModuleConfigsToSchedule = $this->cronConfig->getCronModuleConfigsByTime($roundedTime);
        $this->cronModuleFacade->scheduleModules($cronModuleConfigsToSchedule);
    }

    /**
     * @deprecated Use `runScheduledModulesForInstance` instead
     */
    public function runScheduledModules()
    {
        $cronModuleExecutor = new CronModuleExecutor(self::TIMEOUT_SECONDS);

        $cronModuleConfigs = $this->cronConfig->getAllCronModuleConfigs();

        $scheduledCronModuleConfigs = $this->cronModuleFacade->getOnlyScheduledCronModuleConfigs($cronModuleConfigs);
        $this->runModules($cronModuleExecutor, $scheduledCronModuleConfigs);
    }

    /**
     * @param string $instanceName
     */
    public function runScheduledModulesForInstance(string $instanceName): void
    {
        $cronModuleExecutor = new CronModuleExecutor(self::TIMEOUT_SECONDS);

        $cronModuleConfigs = $this->cronConfig->getCronModuleConfigsForInstance($instanceName);

        $scheduledCronModuleConfigs = $this->cronModuleFacade->getOnlyScheduledCronModuleConfigs($cronModuleConfigs);
        $this->runModulesForInstance($cronModuleExecutor, $scheduledCronModuleConfigs, $instanceName);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronModuleExecutor $cronModuleExecutor
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     * @deprecated Use `runModulesForInstance` instead
     */
    protected function runModules(CronModuleExecutor $cronModuleExecutor, array $cronModuleConfigs)
    {
        $this->runModulesForInstance($cronModuleExecutor, $cronModuleConfigs, CronModuleConfig::DEFAULT_INSTANCE_NAME);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronModuleExecutor $cronModuleExecutor
     * @param array $cronModuleConfigs
     * @param string $instanceName
     */
    protected function runModulesForInstance(CronModuleExecutor $cronModuleExecutor, array $cronModuleConfigs, string $instanceName): void
    {
        $this->logger->addInfo(sprintf('====== Start of cron instance %s ======', $instanceName));

        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $this->runModule($cronModuleExecutor, $cronModuleConfig);
            if ($cronModuleExecutor->canRun() === false) {
                break;
            }
        }

        $this->logger->addInfo(sprintf('======= End of cron instance %s =======', $instanceName));
    }

    /**
     * @param string $serviceId
     */
    public function runModuleByServiceId($serviceId)
    {
        $cronModuleConfig = $this->cronConfig->getCronModuleConfigByServiceId($serviceId);

        $cronModuleExecutor = new CronModuleExecutor(self::TIMEOUT_SECONDS);
        $this->runModule($cronModuleExecutor, $cronModuleConfig);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\CronModuleExecutor $cronModuleExecutor
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     */
    protected function runModule(CronModuleExecutor $cronModuleExecutor, CronModuleConfig $cronModuleConfig)
    {
        $this->logger->addInfo('Start of ' . $cronModuleConfig->getServiceId());
        $cronModuleService = $cronModuleConfig->getService();
        $cronModuleService->setLogger($this->logger);
        $status = $cronModuleExecutor->runModule(
            $cronModuleService,
            $this->cronModuleFacade->isModuleSuspended($cronModuleConfig)
        );

        if ($status === CronModuleExecutor::RUN_STATUS_OK) {
            $this->cronModuleFacade->unscheduleModule($cronModuleConfig);
            $this->logger->addInfo('End of ' . $cronModuleConfig->getServiceId());
        } elseif ($status === CronModuleExecutor::RUN_STATUS_SUSPENDED) {
            $this->cronModuleFacade->suspendModule($cronModuleConfig);
            $this->logger->addInfo('Suspend ' . $cronModuleConfig->getServiceId());
        } elseif ($status === CronModuleExecutor::RUN_STATUS_TIMEOUT) {
            $this->logger->info('Cron reached timeout.');
        }
    }

    /**
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getAll()
    {
        return $this->cronConfig->getAllCronModuleConfigs();
    }

    /**
     * @param string $instanceName
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getAllForInstance(string $instanceName): array
    {
        return $this->cronConfig->getCronModuleConfigsForInstance($instanceName);
    }

    /**
     * @return string[]
     */
    public function getInstanceNames(): array
    {
        return array_unique(array_map(function (CronModuleConfig $config) {
            return $config->getInstanceName();
        }, $this->getAll()));
    }
}
