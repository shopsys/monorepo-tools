<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTimeImmutable;
use DateTimeInterface;
use SS6\ShopBundle\Component\Cron\Config\CronConfig;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;
use SS6\ShopBundle\Component\Cron\CronModuleFacade;
use Symfony\Bridge\Monolog\Logger;

class CronFacade {

	/**
	 * @var \Symfony\Bridge\Monolog\Logger
	 */
	private $logger;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\Config\CronConfig
	 */
	private $cronConfig;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronModuleFacade
	 */
	private $cronModuleFacade;

	/**
	 * @var \DateTimeImmutable|null
	 */
	private $canRunTo;

	public function __construct(Logger $logger, CronConfig $cronConfig, CronModuleFacade $cronModuleFacade) {
		$this->logger = $logger;
		$this->cronConfig = $cronConfig;
		$this->cronModuleFacade = $cronModuleFacade;
	}

	/**
	 * @param \DateTimeInterface $roundedTime
	 */
	public function runModulesByTime(DateTimeInterface $roundedTime) {
		$this->canRunTo = new DateTimeImmutable('+4 minutes');
		$cronModulesConfigsToSchedule = $this->cronConfig->getCronModuleConfigsByTime($roundedTime);
		$this->cronModuleFacade->scheduleModules($cronModulesConfigsToSchedule);

		$cronModuleConfigs = $this->cronConfig->getAll();
		$scheduledCronModuleConfigs = $this->cronModuleFacade->getOnlyScheduledCronModuleConfigs($cronModuleConfigs);
		$this->runModules($scheduledCronModuleConfigs);
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
	 */
	private function runModules(array $cronModuleConfigs) {
		$this->logger->addInfo('====== Start of cron ======');

		foreach ($cronModuleConfigs as $cronModuleConfig) {
			if ($this->canRun()) {
				$this->runModule($cronModuleConfig);
			} else {
				$this->logger->info('Cron reached timeout.');
				break;
			}
		}

		$this->logger->addInfo('======= End of cron =======');
	}

	/**
	 * @param string $moduleId
	 */
	public function runModuleByModuleId($moduleId) {
		$this->canRunTo = new DateTimeImmutable('+4 minutes');
		$this->runModule($this->cronConfig->getCronModuleConfigByModuleId($moduleId));
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
	 */
	private function runModule(CronModuleConfig $cronModuleConfig) {
		$this->logger->addInfo('Start of ' . $cronModuleConfig->getModuleId());
		$cronModuleService = $cronModuleConfig->getCronModuleService();

		if ($cronModuleService instanceof CronModuleInterface) {
			$cronModuleService->run($this->logger);
			$this->cronModuleFacade->unscheduledModule($cronModuleConfig->getModuleId());
		} elseif ($cronModuleService instanceof IteratedCronModuleInterface) {
			$cronModuleService->initialize($this->logger);
			$inProgress = true;
			while ($this->canRun() && $inProgress === true) {
				$inProgress = $cronModuleService->iterate();
			}
			if ($inProgress) {
				$this->cronModuleFacade->suspendModule($cronModuleConfig->getModuleId());
			} else {
				$this->cronModuleFacade->unscheduledModule($cronModuleConfig->getModuleId());
			}
		}

		$this->logger->addInfo('End of ' . $cronModuleConfig->getModuleId());
	}

	/**
	 * @return bool
	 */
	private function canRun() {
		$now = new DateTimeImmutable();

		return $this->canRunTo->diff($now)->invert === 1;
	}
}
