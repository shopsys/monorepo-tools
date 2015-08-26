<?php

namespace SS6\ShopBundle\Component\Cron\Config\Exception;

use Exception;
use SS6\ShopBundle\Component\Cron\Config\Exception\CronConfigException;

class CronServiceConfigNotFoundException extends Exception implements CronConfigException {

	/**
	 * @param string $serviceId
	 * @param \Exception $previous
	 */
	public function __construct($serviceId, Exception $previous = null) {
		parent::__construct('Cron service config with service ID "' . $serviceId . '" not found.', 0, $previous);
	}
}
