<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestCase;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class CloseNewlyOpenedWindowsHelper extends Module {

	// @codingStandardsIgnoreStart
	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	public function _after(TestCase $test) {
		// @codingStandardsIgnoreEnd
		$webDriver = $this->getModule('WebDriver');
		/* @var $webDriver \Codeception\Module\WebDriver */

		$this->closeNewlyOpenedWindows($webDriver->webDriver);
	}

	/**
	 * @param \RemoteWebDriver $webDriver
	 */
	private function closeNewlyOpenedWindows(RemoteWebDriver $webDriver) {
		$handles = $webDriver->getWindowHandles();
		$firstHandle = array_shift($handles);
		foreach ($handles as $handle) {
			$webDriver->switchTo()->window($handle);
			$webDriver->execute(DriverCommand::CLOSE, []);
		}
		$webDriver->switchTo()->window($firstHandle);
	}
}
