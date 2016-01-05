<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use DateTimeInterface;
use SS6\ShopBundle\Component\Cron\CronTimeResolver;

class CronConfig {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronTimeResolver
	 */
	private $cronTimeResolver;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	private $cronModuleConfigs;

	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronTimeResolver $cronTimeResolver
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[] $cronModuleConfigs
	 */
	public function __construct(
		CronTimeResolver $cronTimeResolver,
		array $cronModuleConfigs
	) {
		$this->cronModuleConfigs = $cronModuleConfigs;
		$this->cronTimeResolver = $cronTimeResolver;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	public function getAll() {
		return $this->cronModuleConfigs;
	}

	/**
	 * @param \DateTimeInterface $roundedTime
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronModuleConfig[]
	 */
	public function getCronModuleConfigsByTime(DateTimeInterface $roundedTime) {
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
	public function getCronModuleConfigByModuleId($moduleId) {
		foreach ($this->cronModuleConfigs as $cronConfig) {
			if ($cronConfig->getModuleId() === $moduleId) {
				return $cronConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\Cron\Config\Exception\CronModuleConfigNotFoundException($moduleId);
	}

}
