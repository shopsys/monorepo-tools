<?php

namespace SS6\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\Util\Uri;

class WebDriverHelper extends Module {

	/**
	 * @return \Codeception\Module\WebDriver
	 */
	private function getWebDriver() {
		return $this->getModule('WebDriver');
	}

	/**
	 * @param string $page
	 */
	public function seeCurrentPageEquals($page) {
		$expectedUrl = Uri::appendPath($this->getWebDriver()->_getUrl(), $page);
		$currentUrl = $this->getWebDriver()->webDriver->getCurrentURL();

		$this->assertSame($expectedUrl, $currentUrl);
	}

}
