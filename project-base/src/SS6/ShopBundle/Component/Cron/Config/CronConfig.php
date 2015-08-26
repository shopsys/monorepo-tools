<?php

namespace SS6\ShopBundle\Component\Cron\Config;

use DateTime;
use SS6\ShopBundle\Component\Cron\CronTimeResolver;

class CronConfig {

	/**
	 * @var \SS6\ShopBundle\Component\Cron\CronTimeResolver
	 */
	private $cronTimeResolver;

	/**
	 * @var \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[]
	 */
	private $cronServiceConfigs;

	/**
	 * @param \SS6\ShopBundle\Component\Cron\CronTimeResolver $cronTimeResolver
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[] $cronServiceConfigs
	 */
	public function __construct(
		CronTimeResolver $cronTimeResolver,
		array $cronServiceConfigs
	) {
		$this->cronServiceConfigs = $cronServiceConfigs;
		$this->cronTimeResolver = $cronTimeResolver;
	}

	/**
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[]
	 */
	public function getAll() {
		return $this->cronServiceConfigs;
	}

	/**
	 * @param \DateTime $roundedTime
	 * @return \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[]
	 */
	public function getCronServiceConfigsByTime(DateTime $roundedTime) {
		$matchedCronConfigs = [];

		foreach ($this->cronServiceConfigs as $cronConfig) {
			if ($this->cronTimeResolver->isValidAtTime($cronConfig, $roundedTime)) {
				$matchedCronConfigs[] = $cronConfig;
			}
		}

		return $matchedCronConfigs;
	}

	/**
	 * @param string $serviceId
	 */
	public function getCronServiceConfigByServiceId($serviceId) {
		foreach ($this->cronServiceConfigs as $cronConfig) {
			if ($cronConfig->getServiceId() === $serviceId) {
				return $cronConfig;
			}
		}

		throw new \SS6\ShopBundle\Component\Cron\Config\Exception\CronServiceConfigNotFoundException($serviceId);
	}

}
