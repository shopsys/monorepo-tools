<?php

namespace Shopsys\ShopBundle\Tests\Acceptance\acceptance\PageObject;

use Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

abstract class AbstractPage {

	/**
	 * @var \Facebook\WebDriver\WebDriver
	 */
	protected $webDriver;

	/**
	 * @var \Shopsys\ShopBundle\Tests\Test\Codeception\AcceptanceTester
	 */
	protected $tester;

	public function __construct(StrictWebDriver $strictWebDriver, AcceptanceTester $tester) {
		$this->webDriver = $strictWebDriver->webDriver;
		$this->tester = $tester;
	}

}
