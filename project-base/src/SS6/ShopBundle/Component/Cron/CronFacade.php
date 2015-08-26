<?php

namespace SS6\ShopBundle\Component\Cron;

use DateTime;
use SS6\ShopBundle\Component\Cron\Config\CronConfig;
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
	public function runServicesForTime(DateTime $roundedTime) {
		$this->runServices($this->cronConfig->getCronServiceConfigsByTime($roundedTime));
	}

	/**
	 * @param \SS6\ShopBundle\Component\Cron\Config\CronServiceConfig[] $cronServiceConfigs
	 */
	private function runServices(array $cronServiceConfigs) {
		$this->logger->addInfo('====== Start of cron ======');

		foreach ($cronServiceConfigs as $cronServiceConfig) {
			$this->logger->addInfo('Start of ' . $cronServiceConfig->getServiceId());
			$cronServiceConfig->getCronService()->run($this->logger);
		}

		$this->logger->addInfo('======= End of cron =======');
	}

	/**
	 * @param string $serviceId
	 */
	public function runServiceByServiceId($serviceId) {
		$this->runServices([$this->cronConfig->getCronServiceConfigByServiceId($serviceId)]);
	}
}
