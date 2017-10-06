<?php

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

class DomainHelper extends Module
{
    // @codingStandardsIgnoreStart
    /**
     * {@inheritDoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _before(TestInterface $test)
    {
        // @codingStandardsIgnoreEnd
        $webDriver = $this->getModule(StrictWebDriver::class);
        /* @var $webDriver \Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver */
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /* @var $symfonyHelper \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper */
        $domain = $symfonyHelper->grabServiceFromContainer('shopsys.shop.component.domain');
        /* @var $domain \Shopsys\ShopBundle\Component\Domain\Domain */

        $domainConfig = $domain->getDomainConfigById(1);

        $webDriver->_reconfigure(['url' => $domainConfig->getUrl()]);
    }
}
