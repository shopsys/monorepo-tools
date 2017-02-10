<?php

namespace Shopsys\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class CloseNewlyOpenedWindowsHelper extends Module {

	// @codingStandardsIgnoreStart
	/**
	 * {@inheritDoc}
	 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
	 */
	public function _after(TestInterface $test) {
		// @codingStandardsIgnoreEnd
		$webDriver = $this->getModule(StrictWebDriver::class);
		/* @var $webDriver \Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver */

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
