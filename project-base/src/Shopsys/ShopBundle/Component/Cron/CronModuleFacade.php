<?php

namespace Shopsys\ShopBundle\Component\Cron;

use Doctrine\ORM\EntityManager;
use Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig;
use Shopsys\ShopBundle\Component\Cron\CronModuleRepository;
use Shopsys\ShopBundle\Component\Cron\CronService;

class CronModuleFacade {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Shopsys\ShopBundle\Component\Cron\CronModuleRepository
     */
    private $cronModuleRepository;

    /**
     * @var \Shopsys\ShopBundle\Component\Cron\CronService
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
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
     */
    public function scheduleModules(array $cronModulesConfigs) {
        foreach ($cronModulesConfigs as $cronModuleConfig) {
            $cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
            $cronModule->schedule();
            $this->em->flush($cronModule);
        }
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
     * @return \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig[]
     */
    public function getOnlyScheduledCronModuleConfigs(array $cronModulesConfigs) {
        $scheduledCronModuleIds = $this->cronModuleRepository->getAllScheduledCronModuleIds();

        return $this->cronService->filterScheduledCronModuleConfigs($cronModulesConfigs, $scheduledCronModuleIds);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     */
    public function unscheduleModule(CronModuleConfig $cronModuleConfig) {
        $cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
        $cronModule->unschedule();
        $this->em->flush($cronModule);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     */
    public function suspendModule(CronModuleConfig $cronModuleConfig) {
        $cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
        $cronModule->suspend();
        $this->em->flush($cronModule);
    }

    /**
     * @param \Shopsys\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
     * @return bool
     */
    public function isModuleSuspended(CronModuleConfig $cronModuleConfig) {
        $cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());

        return $cronModule->isSuspended();
    }

}
