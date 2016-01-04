<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTimeImmutable;
use SS6\ShopBundle\Component\Cron\Config\CronModuleConfig;

class CronModuleExecutor {

	const RUN_STATUS_OK = 'ok';
	const RUN_STATUS_TIMEOUT = 'timeout';
	const RUN_STATUS_SUSPENDED = 'suspended';

	/**
	 * @var \DateTimeImmutable|null
	 */
	private $canRunTo;

	/**
	 * @param int $secondsTimeout
	 */
	public function __construct($secondsTimeout) {
		$this->canRunTo = new DateTimeImmutable('+' . $secondsTimeout . ' sec');
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig $cronModuleConfig
	 * @return string
	 */
	public function runModule(CronModuleConfig $cronModuleConfig) {
		$cronModuleService = $cronModuleConfig->getCronModuleService();

		if (!$this->canRun()) {
			return self::RUN_STATUS_TIMEOUT;
		}

		if ($cronModuleService instanceof CronModuleInterface) {
			$cronModuleService->run();

			return self::RUN_STATUS_OK;
		} elseif ($cronModuleService instanceof IteratedCronModuleInterface) {
			$inProgress = true;
			while ($this->canRun() && $inProgress === true) {
				$inProgress = $cronModuleService->iterate();
			}

			return $inProgress ? self::RUN_STATUS_SUSPENDED : self::RUN_STATUS_OK;
		}
	}

	/**
	 * @return bool
	 */
	public function canRun() {
		return $this->canRunTo > new DateTimeImmutable();
	}
}
