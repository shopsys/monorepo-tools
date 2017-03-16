<?php

namespace Tests\ShopBundle\Acceptance\acceptance\PageObject;

use Tests\ShopBundle\Test\Codeception\AcceptanceTester;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

abstract class AbstractPage
{
    /**
     * @var \Facebook\WebDriver\WebDriver
     */
    protected $webDriver;

    /**
     * @var \Tests\ShopBundle\Test\Codeception\AcceptanceTester
     */
    protected $tester;

    public function __construct(StrictWebDriver $strictWebDriver, AcceptanceTester $tester)
    {
        $this->webDriver = $strictWebDriver->webDriver;
        $this->tester = $tester;
    }
}
