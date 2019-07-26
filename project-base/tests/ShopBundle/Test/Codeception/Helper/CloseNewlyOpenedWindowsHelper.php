<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

class CloseNewlyOpenedWindowsHelper extends Module
{
    /**
     * {@inheritDoc}
     */
    public function _after(TestInterface $test)
    {
        /** @var \Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver $webDriver */
        $webDriver = $this->getModule(StrictWebDriver::class);

        if ($webDriver->webDriver === null) {
            // Workaround: When Codeception fails to connect to Selenium WebDriver,
            // the \Codeception\Module\WebDriver::_before() method fails
            // and $webDriver->webDriver is null but _after methods are still run.
            return;
        }

        $this->closeNewlyOpenedWindows($webDriver->webDriver);
    }

    /**
     * @param \Facebook\WebDriver\Remote\RemoteWebDriver $webDriver
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
