<?php

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
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
        $domain = $symfonyHelper->grabServiceFromContainer(Domain::class);
        /* @var $domain \Shopsys\FrameworkBundle\Component\Domain\Domain */

        $domainConfig = $domain->getDomainConfigById(1);

        $webDriver->_reconfigure(['url' => $domainConfig->getUrl()]);
    }
}
