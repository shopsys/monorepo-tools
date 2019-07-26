<?php

declare(strict_types=1);

namespace Tests\ShopBundle\Test\Codeception\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver;

class DomainHelper extends Module
{
    /**
     * {@inheritDoc}
     */
    public function _before(TestInterface $test)
    {
        /** @var \Tests\ShopBundle\Test\Codeception\Module\StrictWebDriver $webDriver */
        $webDriver = $this->getModule(StrictWebDriver::class);
        /** @var \Tests\ShopBundle\Test\Codeception\Helper\SymfonyHelper $symfonyHelper */
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /** @var \Shopsys\FrameworkBundle\Component\Domain\Domain $domain */
        $domain = $symfonyHelper->grabServiceFromContainer(Domain::class);

        $domainConfig = $domain->getDomainConfigById(1);

        $webDriver->_reconfigure(['url' => $domainConfig->getUrl()]);
    }
}
