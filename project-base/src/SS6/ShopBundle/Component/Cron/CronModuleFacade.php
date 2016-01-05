<?php

namespace SS6\ShopBundle\Component\Cron;

use Doctrine\ORM\EntityManager;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;
use SS6\ShopBundle\Component\Cron\CronModuleRepository;
use SS6\ShopBundle\Component\Cron\CronService;

class CronModuleFacade {

	/**
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronModuleRepository
	 */
	private $cronModuleRepository;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronService
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
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
	 */
	public function scheduleModules(array $cronModulesConfigs) {
		foreach ($cronModulesConfigs as $cronModuleConfig) {
			$cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
			$cronModule->schedule();
			$this->em->flush($cronModule);
		}
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModulesConfigs
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	public function getOnlyScheduledCronModuleConfigs(array $cronModulesConfigs) {
		$scheduledCronModuleIds = $this->cronModuleRepository->getAllScheduledCronModuleIds();

		return $this->cronService->filterScheduledCronModuleConfigs($cronModulesConfigs, $scheduledCronModuleIds);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
	 */
	public function unscheduleModule(CronModuleConfig $cronModuleConfig) {
		$cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
		$cronModule->unschedule();
		$this->em->flush($cronModule);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
	 */
	public function suspendModule(CronModuleConfig $cronModuleConfig) {
		$cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());
		$cronModule->suspend();
		$this->em->flush($cronModule);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
	 * @return bool
	 */
	public function isModuleSuspended(CronModuleConfig $cronModuleConfig) {
		$cronModule = $this->cronModuleRepository->getCronModuleByCronModuleId($cronModuleConfig->getModuleId());

		return $cronModule->isSuspended();
	}

}
