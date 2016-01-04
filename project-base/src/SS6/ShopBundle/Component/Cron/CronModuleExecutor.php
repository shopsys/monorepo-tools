<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTimeImmutable;

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

	//@codingStandardsIgnoreStart
	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronModuleInterface|\SS6\ShopBundle\Component\Cron\IteratedCronModuleInterface $cronModuleService
	 * @param bool $suspended
	 * @return string
	 */
	public function runModule($cronModuleService, $suspended) {
		//@codingStandardsIgnoreStop
		if (!$this->canRun()) {
			return self::RUN_STATUS_TIMEOUT;
		}

		if ($cronModuleService instanceof CronModuleInterface) {
			$cronModuleService->run();

			return self::RUN_STATUS_OK;
		} elseif ($cronModuleService instanceof IteratedCronModuleInterface) {
			if ($suspended) {
				$cronModuleService->wakeUp();
			}
			$inProgress = true;
			while ($this->canRun() && $inProgress === true) {
				$inProgress = $cronModuleService->iterate();
			}
			if ($inProgress === true) {
				$cronModuleService->sleep();
				return self::RUN_STATUS_SUSPENDED;
			} else {
				return self::RUN_STATUS_OK;
			}
		}
	}

	/**
	 * @return bool
	 */
	public function canRun() {
		return $this->canRunTo > new DateTimeImmutable();
	}

}
