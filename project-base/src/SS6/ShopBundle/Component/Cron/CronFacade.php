<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTime;
use SS6\ShopBundle\Component\Cron\Config\CronConfig;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;
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

	public function __construct(Logger $logger, CronConfig $cronConfig) {
		$this->logger = $logger;
		$this->cronConfig = $cronConfig;
	}

	/**
	 * @param \DateTime $roundedTime
	 */
	public function runModulesByTime(DateTime $roundedTime) {
		$this->runModules($this->cronConfig->getCronModuleConfigsByTime($roundedTime));
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
	 */
	private function runModules(array $cronModuleConfigs) {
		$this->logger->addInfo('====== Start of cron ======');

		foreach ($cronModuleConfigs as $cronModuleConfig) {
			$this->runModule($cronModuleConfig);
		}

		$this->logger->addInfo('======= End of cron =======');
	}

	/**
	 * @param string $moduleId
	 */
	public function runModuleByModuleId($moduleId) {
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
		} elseif ($cronModuleService instanceof IteratedCronModuleInterface) {
			$cronModuleService->initialize($this->logger);
			// @codingStandardsIgnoreStart
			while ($cronModuleService->iterate()) {}
			// @codingStandardsIgnoreEnd
		}

		$this->logger->addInfo('End of ' . $cronModuleConfig->getModuleId());
	}
}
