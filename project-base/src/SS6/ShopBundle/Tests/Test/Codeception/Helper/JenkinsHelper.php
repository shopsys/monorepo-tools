<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;

class JenkinsHelper extends Module {

	const BASE_SELENIUM_PORT = 4444;

	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	// @codingStandardsIgnoreStart
	public function _initialize() {
	// @codingStandardsIgnoreEnd
		$webDriver = $this->getModule('WebDriver');
		/* @var $webDriver \Codeception\Module\WebDriver */

		$executorNumber = getenv('EXECUTOR_NUMBER');

		if ($executorNumber !== false) {
			$webDriver->_setConfig([
				'port' => self::BASE_SELENIUM_PORT + $executorNumber,
			]);
		}
	}

}
