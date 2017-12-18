<?php

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

class CloseNewlyOpenedWindowsHelper extends Module
{
    // @codingStandardsIgnoreStart
    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _after(TestInterface $test)
    {
        // @codingStandardsIgnoreEnd
        $webDriver = $this->getModule(StrictWebDriver::class);
        /* @var $webDriver \Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver */

        if ($webDriver->webDriver === null) {
            // Workaround: When Codeception fails to connect to Selenium WebDriver,
            // the \Codeception\Module\WebDriver::_before() method fails
            // and $webDriver->webDriver is null but _after methods are still run.
            return;
        }

        $this->closeNewlyOpenedWindows($webDriver->webDriver);
    }

    /**
     * @param \RemoteWebDriver $webDriver
     */
    private function closeNewlyOpenedWindows(RemoteWebDriver $webDriver)
    {
        $handles = $webDriver->getWindowHandles();
        $firstHandle = array_shift($handles);
        foreach ($handles as $handle) {
            $webDriver->switchTo()->window($handle);
            $webDriver->execute(DriverCommand::CLOSE, []);
        }
        $webDriver->switchTo()->window($firstHandle);
    }
}
