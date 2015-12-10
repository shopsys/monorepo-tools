<?php

namespace SS6\ShopBundle\Tests\Test\Codeception;

use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void haveFriend($name, $actorClass = null)
 * @method \Codeception\Scenario getScenario()
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {

	const DEFAULT_AJAX_TIMEOUT_SEC = 10;
	const WAIT_TIMEOUT_SEC = 10;

	use _generated\AcceptanceTesterActions;

	public function switchToLastOpenedWindow() {
		$this->executeInSelenium(function (RemoteWebDriver $webdriver) {
			$handles = $webdriver->getWindowHandles();
			$lastWindow = end($handles);
			$this->switchToWindow($lastWindow);
		});
		$this->waitForElement('body', self::WAIT_TIMEOUT_SEC);
	}

	/**
	 * @param int $timeout
	 */
	public function waitForAjax($timeout = self::DEFAULT_AJAX_TIMEOUT_SEC) {
		$this->waitForJS('return $.active == 0;', $timeout);
	}

}
