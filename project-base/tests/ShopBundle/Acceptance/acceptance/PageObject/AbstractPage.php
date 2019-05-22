<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject;

use Tests\ShopBundle\Test\Codeception\AcceptanceTester;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

abstract class AbstractPage
{
    /**
     * @var \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected $webDriver;

    /**
     * @var \Tests\ShopBundle\Test\Codeception\AcceptanceTester
     */
    protected $tester;

    /**
     * @param \Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver $strictWebDriver
     * @param \Tests\ShopBundle\Test\Codeception\AcceptanceTester $tester
     */
    public function __construct(StrictWebDriver $strictWebDriver, AcceptanceTester $tester)
    {
        $this->webDriver = $strictWebDriver->webDriver;
        $this->tester = $tester;
    }
}
