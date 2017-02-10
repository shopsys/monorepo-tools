<?php

namespace Shopsys\ShopBundle\Tests\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\Util\Uri;
use Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver;

class WebDriverHelper extends Module
{

    /**
     * @return \Shopsys\ShopBundle\Tests\Test\Codeception\Module\StrictWebDriver
     */
    private function getWebDriver() {
        return $this->getModule(StrictWebDriver::class);
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
