<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use Doctrine\ORM\EntityManager;
use Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig;

class CronModuleFacade
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronModuleRepository
     */
    private $cronModuleRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Cron\CronService
     */
    private $cronService;

    public function __construct(
        EntityManager $em,
        CronModuleRepository $cronModuleRepository,
        CronService $cronService
    ) {
        $this->em = $em;
        $this->cronModuleRepository = $cronModuleRepository;
        $this->cronService = $cronService;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     */
    public function scheduleModules(array $cronModuleConfigs)
    {
        foreach ($cronModuleConfigs as $cronModuleConfig) {
            $cronModule = $this->cronModuleRepository->getCronModuleByServiceId($cronModuleConfig->getServiceId());
            $cronModule->schedule();
            $this->em->flush($cronModule);
        }
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
     * @return \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getOnlyScheduledCronModuleConfigs(array $cronModuleConfigs)
    {
        $scheduledServiceIds = $this->cronModuleRepository->getAllScheduledCronModuleServiceIds();

        return $this->cronService->filterScheduledCronModuleConfigs($cronModuleConfigs, $scheduledServiceIds);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     */
    public function unscheduleModule(CronModuleConfig $cronModuleConfig)
    {
        $cronModule = $this->cronModuleRepository->getCronModuleByServiceId($cronModuleConfig->getServiceId());
        $cronModule->unschedule();
        $this->em->flush($cronModule);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     */
    public function suspendModule(CronModuleConfig $cronModuleConfig)
    {
        $cronModule = $this->cronModuleRepository->getCronModuleByServiceId($cronModuleConfig->getServiceId());
        $cronModule->suspend();
        $this->em->flush($cronModule);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     * @return bool
     */
    public function isModuleSuspended(CronModuleConfig $cronModuleConfig)
    {
        $cronModule = $this->cronModuleRepository->getCronModuleByServiceId($cronModuleConfig->getServiceId());

        return $cronModule->isSuspended();
    }
}
