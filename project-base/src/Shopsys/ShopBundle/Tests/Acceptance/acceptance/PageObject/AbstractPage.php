<?php

namespace SS6\ShopBundle\Tests\Acceptance\acceptance\PageObject;

use SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use SS6\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

abstract class AbstractPage {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	protected $webDriver;

	/**
	 * @var \SS6\ShopBundle\Tests\Test\Codeception\AcceptanceTester
	 */
	protected $tester;

	public function __construct(StrictWebDriver $strictWebDriver, AcceptanceTester $tester) {
		$this->webDriver = $strictWebDriver->webDriver;
		$this->tester = $tester;
	}

}
